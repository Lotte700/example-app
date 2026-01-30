@extends('layouts.app')


@section('title', 'Edit Product')


@section('content')
<form action="{{ route('products.update', $product->id) }}" method="POST">
@csrf
@method('PUT')


<div class="mb-2">
<label>Code</label>
<input type="text" name="code" value="{{ $product->code }}" class="form-control">
</div>


<div class="mb-2">
<label>Name</label>
<input type="text" name="name" value="{{ $product->name }}" class="form-control">
</div>



<div class="mb-2">
<label>Category</label>
<select name="category_id" class="form-control">
@foreach($categories as $category)
<option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
{{ $category->category_name }}
</option>
@endforeach
</select>
</div>


<button class="btn btn-primary">Update</button>
</form>
@endsection