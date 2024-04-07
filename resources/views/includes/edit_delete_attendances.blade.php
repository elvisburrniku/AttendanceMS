<!-- Edit -->
<div class="modal fade" id="edit{{ $attendance->user_id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>{{ \Carbon\Carbon::parse($attendance->upload_time)->toDateString() }} - {{ $attendance->first_name }} {{ $attendance->last_name }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>
            <form class="form-horizontal" method="POST" action="{{ route('attendance.update', $attendance->user_id) }}">
                <div class="modal-body text-left">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="date" value="{{ request()->date ?? now()->format('Y-m-d') }}">
                    <div class="form-group">
                        <label for="emp_code">Hyrje</label>
                        <input type="time" class="form-control" value="{{ $attendance->checkin_time_date }}" placeholder="Hyrje" id="checkin_time" name="checkin_time"/>
                    </div>
                    <div class="form-group">
                        <label for="emp_code">Pauza 1</label>
                        <input type="time" class="form-control" value="{{ $attendance->break_in_time }}" placeholder="Pauza 1" id="break_in_time" name="break_in_time"/>
                    </div>

                    <div class="form-group">
                        <label for="emp_code">Pauza 2</label>
                        <input type="time" class="form-control" value="{{ $attendance->break_out_time }}" placeholder="Shkruaj kodin e zonës" id="break_out_time" name="break_out_time"/>
                    </div>

                    <div class="form-group">
                        <label for="emp_code">Dalje</label>
                        <input type="time" class="form-control" value="{{ $attendance->checkout_time }}" placeholder="Shkruaj kodin e zonës" id="checkout_time" name="checkout_time"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i
                            class="fa fa-close"></i> Anulo</button>
                    <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i>
                        Përditso</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
