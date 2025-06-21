@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Time Interval Management</h4>
                        <div>
                            <a href="{{ route('time-intervals.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> New Time Interval
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
                        <table class="table table-striped table-bordered" id="timeIntervalsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Start Time</th>
                                    <th>Duration</th>
                                    <th>Work Hours</th>
                                    <th>Status</th>
                                    <th>Used in Shifts</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timeIntervals as $interval)
                                <tr>
                                    <td>{{ $interval->id }}</td>
                                    <td>
                                        <strong>{{ $interval->alias }}</strong>
                                    </td>
                                    <td>
                                        {{ $interval->formatted_in_time }}
                                    </td>
                                    <td>
                                        {{ $interval->duration }} minutes
                                        <br><small class="text-muted">{{ $interval->duration_in_hours }} hours</small>
                                    </td>
                                    <td>
                                        {{ number_format($interval->work_day, 1) }} day(s)
                                    </td>
                                    <td>
                                        @if($interval->use_mode > 0)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $interval->shifts->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('time-intervals.show', $interval) }}" class="btn btn-info btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('time-intervals.edit', $interval) }}" class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('time-intervals.toggle', $interval) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn {{ $interval->use_mode > 0 ? 'btn-secondary' : 'btn-success' }} btn-sm">
                                                    <i class="fa {{ $interval->use_mode > 0 ? 'fa-pause' : 'fa-play' }}"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="deleteTimeInterval({{ $interval->id }})">
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
                        {{ $timeIntervals->links() }}
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
                Are you sure you want to delete this time interval?
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
    $('#timeIntervalsTable').DataTable({
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [7] }
        ]
    });
});

function deleteTimeInterval(id) {
    $('#deleteForm').attr('action', '/time-intervals/' + id);
    $('#deleteModal').modal('show');
}
</script>
@endsection