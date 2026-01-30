@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- ตรวจสอบว่ามี Form คลุมทั้งตารางและปุ่ม Save --}}
    <form action="{{ route('processes.store.bulk') }}" method="POST" id="mainForm">
        @csrf

        {{-- 1. เลือก Process --}}
        <div class="mb-3">
            <label class="fw-bold">Process</label>
            {{-- สังเกต ID: 'process_id_select' เพื่อไม่ให้ซ้ำกับอย่างอื่น --}}
            <select id="process_id_select" class="form-control" required>
                <option value="">-- Select Process --</option>
                @foreach($processes as $p)
                    <option value="{{ $p->id }}">{{ strtoupper($p->name) }}</option>
                @endforeach
            </select>
            <div class="col-md-6">
        <label class="fw-bold">Transaction Date</label>
        {{-- ใส่ค่าเริ่มต้นเป็นวันนี้ (now) --}}
        <input type="date" id="transaction_date" name="transaction_date" 
               class="form-control" value="{{ date('Y-m-d') }}" required>
    </div>

        </div>
<div class="row align-items-end mb-4">
    <div class="col-md-7">
        <label class="fw-bold">Product (Searchable)</label>
        <select id="product_unit_id_select" name="product_unit_id" class="form-control select2">
            <option></option> {{-- สำคัญมาก: ต้องมีเพื่อแสดง Placeholder --}}
            @foreach($product_units as $u)
                <option value="{{ $u->id }}">
                    {{ $u->product->name }} - {{ $u->name }}
                </option>
            @endforeach
        </select>
    </div>
      
            <div class="col-md-3">
                <label class="fw-bold">Quantity</label>
                <input type="number" id="qty_input" class="form-control" min="1" placeholder="0">
            </div>
            <div class="col-md-2">
            <button type="button" id="btn-add-item" class="btn btn-primary w-100">+ Add</button>
            </div>
        </div>

        {{-- 3. ตารางตะกร้าสินค้า --}}
        <table class="table table-bordered mt-3" id="cartTable">
            <thead class="table-light">
                <tr>
                    <th>Process</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th width="80">Remove</th>
                </tr>
            </thead>
            <tbody>
                {{-- ข้อมูลจะถูกแทรกที่นี่โดย JavaScript --}}
            </tbody>
        </table>

        {{-- ปุ่ม Save ต้องมี type="submit" --}}
        <button type="submit" class="btn btn-success mt-3">💾 Save All</button>
    </form>
</div>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@endsection