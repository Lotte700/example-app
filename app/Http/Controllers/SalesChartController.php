<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\DB;
use App\Models\ProductUnit;
use App\Models\ProductUnits;
use Illuminate\Support\Facades\Auth;

class SalesChartController extends Controller{


public function salesReport(Request $request)
{
    $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->format('Y-m-d'));
    $categoryId = $request->get('category_id');
    // รับค่าจากทั้งการคลิกที่กราฟ หรือการพิมพ์ค้นหาในช่อง Search
    $selectedProduct = $request->get('selected_product') ?: $request->get('search_product');

    $categories = \App\Models\Category::all();

    // 1. กราฟบน: ยอดขายรวมแยกตามสินค้า
    $productSalesQuery = Inventory::select([
            'products.name as product_name',
            DB::raw('SUM(ABS(inventories.quantity)) as total_qty'),
            DB::raw('SUM(ABS(inventories.quantity) * product_units.price) as total_price')
        ])
        ->join('product_units', 'inventories.product_unit_id', '=', 'product_units.id')
        ->join('products', 'product_units.product_id', '=', 'products.id')
        ->where('inventories.outlet_id', '=', Auth::user()->employee->outlet_id)
        ->where('inventories.process_id', 1)
        ->where('inventories.status', 'approved')
        ->whereBetween('inventories.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

    if ($categoryId) {
        $productSalesQuery->where('products.category_id', $categoryId);
    }

    $productSales = $productSalesQuery->groupBy('products.name')->get();
    
    // สร้าง Options เพื่อให้กราฟ 1 สามารถคลิกได้ (Event Click)
    $chart1 = (new LarapexChart)->lineChart()
        ->setTitle('ยอดขายรวมแยกตามสินค้า')
        ->addData('จำนวนชิ้น', $productSales->pluck('total_qty')->toArray())
        ->addData('ยอดเงิน (K)', $productSales->map(fn($item) => round($item->total_price / 1000, 2))->toArray())
        ->setXAxis($productSales->pluck('product_name')->toArray())
        ->setHeight(300);

    // 2. กราฟล่าง: แสดงแนวโน้มรายวัน (เมื่อค้นหา หรือ คลิกเลือกสินค้า)
    $chart2 = null;
    if ($selectedProduct) {
        $dailyProductSales = Inventory::select([
                DB::raw('DATE(inventories.created_at) as date'),
                DB::raw('SUM(ABS(inventories.quantity)) as qty'),
                DB::raw('SUM(ABS(inventories.quantity) * product_units.price) as daily_total_price')
            ])
            ->join('product_units', 'inventories.product_unit_id', '=', 'product_units.id')
            ->join('products', 'product_units.product_id', '=', 'products.id')
            ->where('inventories.outlet_id', '=', Auth::user()->employee->outlet_id)
            ->where('products.name', 'LIKE', "%{$selectedProduct}%") // ค้นหาแบบกึ่งตรง
            ->where('inventories.process_id', 1)
            ->where('inventories.status', 'approved')
            ->whereBetween('inventories.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        if ($dailyProductSales->count() > 0) {
            $chart2 = (new LarapexChart)->barChart()
                ->setTitle("แนวโน้มยอดขายรายวัน: $selectedProduct")
                ->addData('จำนวนชิ้น', $dailyProductSales->pluck('qty')->toArray())
                ->addData('ยอดเงิน (K)', $dailyProductSales->map(fn($item) => round($item->daily_total_price / 1000, 2))->toArray())
                ->setXAxis($dailyProductSales->pluck('date')->toArray())
                ->setHeight(300)
                ->setColors(['#008FFB', '#FEB019']);
        }
    }

    $salesData = Inventory::with(['productUnit.product', 'employee'])
        ->where('process_id', 1)
        ->where('inventories.outlet_id', '=', Auth::user()->employee->outlet_id)
        ->where('status', 'approved')
        ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('reports.sales', compact('chart1', 'chart2', 'salesData', 'startDate', 'endDate', 'categories', 'categoryId', 'selectedProduct'));
}

public function index()
{
    // 1. Pending Approvals
    $pendingApprovalsCount = Inventory::where('process_id', '!=', 1)
    ->where('process_id', '!=', 4)
    ->where('status', 'pending')
    ->where('outlet_id', '=', Auth::user()->employee->outlet_id)
    ->count();

    // 2. Today's Revenue
    $todayRevenue = Inventory::where('inventories.status', 'approved')
        ->whereRaw('DATE(inventories.created_at) = ?', [\Carbon\Carbon::now('Asia/Bangkok')->format('Y-m-d')])
        ->where('inventories.process_id', '=', 1)
        ->where('inventories.outlet_id', '=', Auth::user()->employee->outlet_id)
        ->join('product_units', 'inventories.product_unit_id', '=', 'product_units.id')
        ->sum(DB::raw('ABS(inventories.quantity) * product_units.price'));

    // --- ส่วนที่แก้ไขใหม่: คำนวณ Balance จากตาราง Inventories ---
    
    // ดึงยอดคงเหลือของทุก Product Unit (Sum quantity เฉพาะรายการที่ approved)
    $inventoryBalances = Inventory::select([
            'product_unit_id',
            'product_units.price',
            DB::raw('SUM(inventories.quantity) as current_balance')
        ])
        ->join('product_units', 'inventories.product_unit_id', '=', 'product_units.id')
        ->where('inventories.status', 'approved')
        ->where('inventories.outlet_id', Auth::user()->employee->outlet_id) // 👈 สำคัญ: ต้องระบุสาขาตัวเอง
        ->groupBy('product_unit_id', 'product_units.price')
        ->get();

    $inventoryitems = Inventory::select([
        'inventories.product_unit_id',
        'product_units.price',
        'products.name as product_name', // ดึงชื่อสินค้ามาด้วย
        'product_units.name as unit_name', // ดึงชื่อหน่วยมาด้วย
        DB::raw('SUM(inventories.quantity) as current_balance')
    ])
    ->join('product_units', 'inventories.product_unit_id', '=', 'product_units.id')
    ->join('products', 'product_units.product_id', '=', 'products.id') // Join เพิ่มเพื่อให้รู้จัก Item
    ->whereRaw('DATE(inventories.created_at) = ?', [\Carbon\Carbon::now('Asia/Bangkok')->format('Y-m-d')])
    ->where('inventories.status', 'approved')
    ->where('inventories.outlet_id', Auth::user()->employee->outlet_id) // 👈 สำคัญ: ต้องระบุสาขาตัวเอง
    ->groupBy('inventories.product_unit_id', 'product_units.price', 'products.name', 'product_units.name')
    ->get();

    // 3. มูลค่าคลังสินค้าทั้งหมด (Sum of balance * price)
    $totalInventoryValue = $inventoryBalances->sum(function($item) {
        return $item->current_balance * $item->price;
    });

    // 4. สินค้าที่สต็อกต่ำ (Balance < 5 และ มูลค่ารวม < 5000)
    // ... โค้ดส่วนอื่นคงเดิม ...

// 1. ดึงยอดคงเหลือของทุก Product Unit ทั้งหมดในระบบ (ไม่ต้องระบุวันที่)
$inventoryBalances = Inventory::select([
        'inventories.product_unit_id',
        'product_units.price',
        'products.name as product_name',
        'product_units.name as unit_name',
        DB::raw('SUM(inventories.quantity) as current_balance')
    ])
    ->join('product_units', 'inventories.product_unit_id', '=', 'product_units.id')
    ->join('products', 'product_units.product_id', '=', 'products.id')
    ->where('inventories.status', 'approved')
    ->where('inventories.outlet_id', Auth::user()->employee->outlet_id)
    ->groupBy('inventories.product_unit_id', 'product_units.price', 'products.name', 'product_units.name')
    ->get();

// 2. คำนวณมูลค่าคลังสินค้าทั้งหมด (จากยอดทั้งหมด)
$totalInventoryValue = $inventoryBalances->sum(function($item) {
    return $item->current_balance * $item->price;
});

// 3. คำนวณสินค้าที่สต็อกต่ำ (Filter จากยอดทั้งหมด)
$lowStockProducts = $inventoryBalances->filter(function($item) {
    // 1. เช็คว่าเป็นสินค้าที่เหลือน้อยกว่า 10 (และไม่ใช่ค่าติดลบ)
    $isLowQuantity = $item->current_balance > 0 && $item->current_balance < 10;
    
    // 2. ปรับมูลค่าเพิ่มขึ้น หรือ "ไม่เช็คมูลค่า" สำหรับรายการที่จำนวนน้อยจริงๆ
    // ลองปรับเป็น 15,000 หรือตัดเงื่อนไขมูลค่าออกถ้าต้องการนับทุกอย่างที่เหลือน้อย
    $isLowValue = ($item->current_balance * $item->price) < 15000; 

    return $isLowQuantity && $isLowValue;
})->count();

// ... ส่วนที่เหลือส่ง compact ไปที่ View ...

    // --- จบส่วนที่แก้ไข ---

    // 5. Recent Transactions
    $recentTransactions = Inventory::with(['productUnit.product'])
    ->where('inventories.status', 'pending')
    ->where('inventories.outlet_id', '=', Auth::user()->employee->outlet_id)
    ->where('inventories.process_id', '!=', 3)
    ->orderBy('inventories.created_at', 'desc')
    ->latest()
    ->take(5)
    ->get();

    return view('dashboard.index', compact(
        'pendingApprovalsCount', 
        'todayRevenue', 
        'totalInventoryValue', 
        'lowStockProducts', 
        'recentTransactions'
    ));
}
}