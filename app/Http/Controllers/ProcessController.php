<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\Process;
use App\Models\ProductUnits;
use Illuminate\Validation\Rules\In;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;





class ProcessController extends Controller
{
    public function index()
{
    // ดึงเฉพาะรายการที่ยัง pending
    $allProcesses = Inventory::with(['process', 'productUnit.product', 'outlet', 'employee', 'to_outlet'])
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get();

   $openProcesses = $allProcesses->where('process.name', 'open')
   ->where('outlet_id', Auth::user()->employee->outlet_id)
    ->groupBy(function($item) {
        // ตรวจสอบว่าถ้าเป็น String ให้ parse ก่อน ถ้าเป็น Object อยู่แล้วก็เรียก format ได้เลย
        return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
    });

    // 2. แยกกลุ่มอื่นๆ: กรองเอาทุกอย่างที่ไม่ใช่ 'open' (เช่น transfer, sell, spoil)
   $otherProcesses = $allProcesses->filter(function ($item) {
    $processName = strtolower($item->process->name);
    $userOutletId = Auth::user()->employee->outlet_id;

    // เงื่อนไขที่ 1: ถ้าเป็นรายการขาย (sell) หรือ เสีย (spoil) ให้แสดงปกติ
    if (in_array($processName, ['sell', 'spoil'])) {
        return true;
    }

    // เงื่อนไขที่ 2: ถ้าเป็นรายการโอน (transfer) 
    // ต้องไม่ใช่ 'open' และ Outlet ปลายทาง (outlet_id ใน inventory) ต้องไม่ใช่ของตัวเอง
    if ($processName === 'transfer') {
        return $item->outlet_id != $userOutletId;
    }

    return $processName !== 'open';
});

    return view('processes.index', compact('openProcesses', 'otherProcesses'));
}
    
 public function destroy(Inventory $process)
    {
        $process->delete();
        return redirect()->route('processes.index')
            ->with('success', 'ลบสินค้าเรียบร้อย');
    }

    private function createInventory(
    int $productUnitId,
    int $processId,
    int $quantity
) {
    $process = Process::findOrFail($processId);

    // normalize quantity
    if (in_array($process->name, ['sell', 'spoil', 'transfer'])) {
        $quantity = -abs($quantity);
    }

    // status
    $status = in_array($process->name, ['open', 'transfer'])
        ? 'pending'
        : 'approved';

    Inventory::create([
        'product_unit_id' => $productUnitId,
        'outlet_id'       => Auth::user()->employee->outlet_id,
        'employee_id'     => Auth::user()->employee->id,
        'process_id'      => $processId,
        'quantity'        => $quantity,
        'status'          => $status,
    ]);
}

public function create(Request $request)
{
    $product_units = ProductUnits::with('product.category')->get();
    $outlets = Outlet::all();
    $processes = Process::all();

    return view('processes.create', compact(
        'product_units',
        'outlets',
        'processes'
    ));
}


public function approve(Inventory $inventory)
{
    if ($inventory->status !== 'pending') {
        return back();
    }

    DB::transaction(function () use ($inventory) {
        $inventory->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->employee->id,
            'approved_at' => now(),
        ]);

        if ($inventory->process->name === 'transfer') {
            Inventory::create([
                'product_unit_id' => $inventory->product_unit_id,
                'outlet_id'       => Auth::user()->employee->outlet_id, // Outlet ผู้ส่ง
                'employee_id'     => $inventory->employee_id,        // คนที่ขอ
                'process_id'      => $inventory->process_id,
                'quantity'        => -abs($inventory->quantity),
                'status'          => 'approved',
                'approved_by'     => Auth::user()->employee->id,     // คนกดยืนยัน
                'approved_at'     => now(),
            ]);
        }
    });

    return back()->with('success', 'อนุมัติเรียบร้อย');
}


public function reject(Inventory $inventory)
{
    $inventory->update([
        'status' => 'rejected',
        'approved_by' => Auth::user()->employee->id,
        'approved_at' => now(),
    ]);

    return back()->with('success', 'ปฏิเสธการโอนเรียบร้อย');


}
public function storeBulk(Request $request)
{
// 1. เช็คเฉพาะรายการในตะกร้า (items)
    if (!$request->has('items') || empty($request->items)) {
        return redirect()->back()->with('error', 'กรุณาเพิ่มสินค้าลงในตะกร้าก่อนบันทึก');
    }

    // 2. ปรับ Validation ให้ตรวจเฉพาะข้อมูลใน array items เท่านั้น
    $request->validate([
        'items' => 'required|array',
        'items.*.product_unit_id' => 'required|exists:product_units,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.process_id' => 'required|exists:processes,id',
        'items.*.created_at' => 'nullable|date',
    ]);

    try {
        DB::transaction(function () use ($request) {
            foreach ($request->items as $item) {
                $process = Process::findOrFail($item['process_id']);
                $qty = $item['quantity'];

                // ปรับค่า Qty ให้ติดลบถ้าเป็น Sell หรือ Spoil
                if (in_array(strtolower($process->name), ['sell', 'spoil'])) {
                    $qty = -abs($qty);
                }
        

                Inventory::create([
                    'product_unit_id' => $item['product_unit_id'],
                    'quantity'        => $qty,
                    'process_id'      => $item['process_id'],
                    'outlet_id'       => Auth::user()->employee->outlet_id ?? null,
                    'employee_id'     => Auth::user()->employee->id ?? null,
                    'status'          => in_array(strtolower($process->name), ['open', 'transfer']) 
                                         ? 'pending' 
                                         : 'approved',
                    // 👈 บันทึกวันที่ตามที่เลือกมาจากหน้าจอ
                    'created_at' => \Carbon\Carbon::parse($item['created_at'])->setTimeFrom(now()),
                ]);
            }
        });

        return redirect()->route('processes.index')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');

    } catch (\Exception $e) {
    // พ่น Error ออกมาดูเลยว่าทำไมบันทึกไม่เข้า
    dd([
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}

    
}

    
}