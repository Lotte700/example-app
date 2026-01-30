@extends('layouts.app')


@section('title', 'Products')


@section('content')
<a href="{{ route('products.create') }}" class="btn btn-primary mb-3">เพิ่มสินค้า</a>
<a href="{{ route('product_units.create') }}" class="btn btn-primary mb-3">เพิ่มหน่วยสินค้า</a>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif


<table class="table table-bordered">
<tr>
<th>Code</th>
<th>Name</th>
<th>category</th>
<th>Action</th>
</tr>


@foreach($products as $p)
<tr>
<td>{{ $p->code }}</td>
<td>{{ $p->name }}</td>
<td>{{ $p->category->category_name }}</td>

<td>
<a href="{{ route('products.edit', $p->id) }}" class="btn btn-warning btn-sm">Edit</a>


<form action="{{ route('products.destroy', $p->id) }}" method="POST" style="display:inline">
@csrf
@method('DELETE')
<button class="btn btn-danger btn-sm" onclick="return confirm('ลบ?')">Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
@endsection