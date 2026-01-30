@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <form id="filterForm" action="{{ route('report.sales') }}" method="GET" class="card p-3 border-0 shadow-sm mb-4">
        <input type="hidden" name="selected_product" id="selectedProductInput" value="{{ $selectedProduct }}">
        
        <div class="row g-2 align-items-end">
            <form id="filterForm" action="{{ route('report.sales') }}" method="GET">
    <input type="hidden" name="selected_product" id="selectedProductInput" value="{{ $selectedProduct }}">
    
    <div class="row">
        <div class="col-md-4">
            <label>ค้นหาสินค้าเพื่อดูรายวัน</label>
            <input type="text" name="search_product" class="form-control" 
                   placeholder="ชื่อสินค้า..." value="{{ $selectedProduct }}">
        </div>
        </div>
</form>
            
            <div class="col-md-2">
                <label class="small fw-bold text-muted">หมวดหมู่</label>
                <select name="category_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="small fw-bold text-muted">เริ่มต้น</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            
            <div class="col-md-2">
                <label class="small fw-bold text-muted">สิ้นสุด</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            
            <div class="col-md-3">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">กรองข้อมูล</button>
                    <a href="{{ route('report.sales') }}" class="btn btn-outline-secondary">ล้างค่า</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            {!! $chart1->container() !!}
        </div>
    </div>

    @if($chart2)
    <div class="card border-0 shadow-sm mb-4 bg-light border-start border-primary border-4">
        <div class="card-body">
            {!! $chart2->container() !!}
        </div>
    </div>
    @elseif($selectedProduct)
    <div class="alert alert-warning border-0 shadow-sm">
        ไม่พบข้อมูลยอดขายรายวันสำหรับสินค้า: <strong>{{ $selectedProduct }}</strong> ในช่วงเวลาที่เลือก
    </div>
    @endif

</div>


<script src="{{ $chart1->cdn() }}"></script>
{{ $chart1->script() }}
@if($chart2) {{ $chart2->script() }} @endif

<script>
    // รอให้กราฟถูกสร้างเสร็จ
    window.addEventListener('load', function() {
        // ดึง Instance ของ ApexCharts (Larapex มักเก็บไว้ในชื่อแปรสุ่มหรือ window)
        // วิธีที่ง่ายที่สุดคือดักผ่าน Global Event ของ ApexCharts
        
        Apex.chart = {
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    // ตรวจสอบว่าคลิกที่จุดข้อมูลจริง (ไม่ใช่พื้นที่ว่าง)
                    if (config.dataPointIndex !== -1) {
                        // ดึงชื่อสินค้าจากแกน X (Categories) ตาม Index ที่คลิก
                        const productName = config.w.config.xaxis.categories[config.dataPointIndex];
                        
                        // นำชื่อสินค้าไปใส่ในช่อง Input และ Submit ฟอร์ม
                        const selectedInput = document.getElementById('selectedProductInput');
                        const filterForm = document.getElementById('filterForm');
                        
                        if (selectedInput && filterForm) {
                            selectedInput.value = productName;
                            filterForm.submit();
                        }
                    }
                }
            }
        };
    });
</script>
@endsection