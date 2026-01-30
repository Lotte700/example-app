<div class="btn-group">
    {{-- ปุ่มยืนยัน (เฉพาะ manager และ supervisor) --}}
    @if( auth()->user()->employee->role === 'manager' ||
    auth()->user()->employee->role === 'supervisor')
        <form action="{{ route('processes.approve', $process->id) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success btn-sm px-3" onclick="return confirm('Approve this transaction?')">
                Approve
            </button>
        </form>
    @endif

    {{-- ปุ่มลบ --}}
    <form action="{{ route('processes.destroy', $process->id) }}" method="POST" class="d-inline ms-1">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this request?')">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>