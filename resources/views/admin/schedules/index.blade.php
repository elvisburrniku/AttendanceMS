@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Schedule Management</h4>
                        <div>
                            <a href="{{ route('schedules.bulk') }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-users"></i> Bulk Assign
                            </a>
                            <a href="{{ route('schedules.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> New Schedule
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

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="schedulesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Employee</th>
                                    <th>Shift</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->id }}</td>
                                    <td>
                                        @if($schedule->employee)
                                            <strong>{{ $schedule->employee->first_name }} {{ $schedule->employee->last_name }}</strong>
                                            <br><small class="text-muted">{{ $schedule->employee->emp_code }}</small>
                                        @else
                                            <span class="text-muted">Employee not found</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($schedule->shift)
                                            <span class="badge badge-info">{{ $schedule->shift->alias }}</span>
                                        @else
                                            <span class="text-muted">Shift not found</span>
                                        @endif
                                    </td>
                                    <td>{{ $schedule->start_date }}</td>
                                    <td>{{ $schedule->end_date }}</td>
                                    <td>
                                        @if($schedule->isActive())
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('schedules.show', $schedule) }}" class="btn btn-info btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="deleteSchedule({{ $schedule->id }})">
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
                        {{ $schedules->links() }}
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
                Are you sure you want to delete this schedule assignment?
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
    $('#schedulesTable').DataTable({
        order: [[3, 'desc']],
        columnDefs: [
            { orderable: false, targets: [6] }
        ]
    });
});

function deleteSchedule(id) {
    $('#deleteForm').attr('action', '{{ url("schedules") }}/' + id);
    $('#deleteModal').modal('show');
}
</script>
@endsection