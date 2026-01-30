@extends('layouts.app')


@section('title', 'Add Product')


@section('content')
<form action="{{ route('products.store') }}" method="POST">
@csrf


<div class="mb-2">
<label>Code</label>
<input type="text" name="code" class="form-control">
</div>


<div class="mb-2">
<label>Name</label>
<input type="text" name="name" class="form-control">
</div>


<div class="mb-2">
<label>Category</label>
<select name="category_id" class="form-control">
@foreach($categories as $category)
<option value="{{ $category->id }}">{{ $category->category_name }}</option>
@endforeach
</select>
</div>


<button class="btn btn-success">Save</button>
</form>
@endsection