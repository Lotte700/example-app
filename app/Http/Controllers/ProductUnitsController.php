<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductUnits;
use App\Models\Product;

class ProductUnitsController extends Controller
{

    public function create()
    {
        $products = Product::all();
        return view('product_units.create', compact('products'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required',
            'ml' => 'required',
            'price' => 'required|numeric',
        ]);

        ProductUnits::create($request->all());

        return redirect()->route('products.index')
            ->with('success', 'เพิ่มสินค้าเรียบร้อย');
    }

}
