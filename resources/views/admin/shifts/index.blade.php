@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Shift Management</h4>
                        <div>
                            <a href="{{ route('time-intervals.index') }}" class="btn btn-info btn-sm">
                                <i class="fa fa-clock"></i> Time Intervals
                            </a>
                            <a href="{{ route('shifts.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> New Shift
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="shiftsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Shift Name</th>
                                    <th>Time Intervals</th>
                                    <th>Working Hours</th>
                                    <th>Employees Assigned</th>
                                    <th>Settings</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shifts as $shift)
                                <tr>
                                    <td>{{ $shift->id }}</td>
                                    <td>
                                        <strong>{{ $shift->alias }}</strong>
                                        <br><small class="text-muted">Cycle: {{ $shift->shift_cycle }} days</small>
                                    </td>
                                    <td>
                                        @if($shift->timeIntervals->count() > 0)
                                            @foreach($shift->timeIntervals as $interval)
                                                <span class="badge badge-secondary mr-1">{{ $interval->alias }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No intervals assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($shift->timeIntervals->count() > 0)
                                            {{ number_format($shift->timeIntervals->sum('duration') / 60, 1) }} hours
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $shift->schedules->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($shift->work_weekend)
                                                <span class="badge badge-info">Weekend Work</span>
                                            @endif
                                            @if($shift->enable_ot_rule)
                                                <span class="badge badge-warning">OT Enabled</span>
                                            @endif
                                            @if($shift->work_day_off)
                                                <span class="badge badge-secondary">Day Off Work</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('shifts.show', $shift) }}" class="btn btn-info btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('shifts.copy', $shift) }}" class="btn btn-secondary btn-sm">
                                                <i class="fa fa-copy"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="deleteShift({{ $shift->id }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $shifts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this shift? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#shiftsTable').DataTable({
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [6] }
        ]
    });
});

function deleteShift(id) {
    $('#deleteForm').attr('action', '/shifts/' + id);
    $('#deleteModal').modal('show');
}
</script>
@endsection