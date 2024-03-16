<!-- Delete -->
<div class="modal fade" id="delete{{ $attendance->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header " style="align-items: center">
               
              <h4 class="modal-title "><span class="employee_id">Delete Employee</span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('attendances.destroy', $attendance->id) }}">
                    @csrf
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="attendance_ids" value="{{ $attendance->group_ids }}">
                    <div class="text-center">
                        <h6>A jeni i sigurt që dëshironi ta fshini:</h6>
                        <h2 class="bold del_employee_name">{{ \Carbon\Carbon::parse($attendance->upload_time)->toDateString() }} - {{ $attendance->first_name }} {{ $attendance->last_name }}</h2>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i
                        class="fa fa-close"></i> Anulo</button>
                <button type="submit" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i> Fshij</button>
                </form>
            </div>
        </div>
    </div>
</div>
