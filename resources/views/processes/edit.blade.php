@extends('layouts.app')
@section('title', 'Processes')
@section('content')
<a href="{{ route('processes.create') }}" class="btn btn-primary mb-3">
    Add Process
</a>
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
