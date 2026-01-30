@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">Dashboard Overview</h2>
            <p class="text-muted">ยินดีต้อนรับกลับมา! นี่คือสรุปภาพรวมของระบบในวันนี้</p>
        </div>
    </div>

    {{-- ดึงไฟล์ส่วนการ์ดสถิติ --}}
    @include('dashboard._stats_cards')

    {{-- ดึงไฟล์ส่วนเนื้อหาหลัก (ตาราง + ปุ่มกด) --}}
    @include('dashboard._main_content')
</div>
@endsection