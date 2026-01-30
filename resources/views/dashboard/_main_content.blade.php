<div class="row">
    {{-- Recent Transactions --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Transactions required</h6>
                <a href="{{ route('processes.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Day</th>
                                <th>Product</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $tx->created_at->format('D - d ') }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $tx->productUnit->product->name }}</div>
                                        <small class="text-muted">{{ $tx->productUnit->name }}</small>
                                    </td>
                                    <td>
                                        @if($tx->status == 'approved')
                                            <span class="badge bg-success">Success</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 {{ $tx->quantity < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format(abs($tx->quantity)) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No transactions today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-dark">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('processes.create') }}" class="btn btn-primary text-start px-3 py-2 shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i> New Transaction
                    </a>
                    {{-- เพิ่ม Link สำหรับ Export รายงาน --}}
                    <a href="{{ route('report.sales') }}?export=today" class="btn btn-outline-secondary text-start px-3 py-2">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i> Export Daily Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>