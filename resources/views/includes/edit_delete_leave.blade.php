<!-- Edit -->
<div class="modal fade" id="edit{{ $leave->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Përditso Pushimin</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>
            <form class="form-horizontal" method="POST" action="{{ route('leaves.update', $leave->id) }}">
                <div class="modal-body text-left">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="emp_code">Tipi</label>
                        <input type="text" class="form-control" value="{{ $leave->type }}" placeholder="Shkruaj tipin e pushimit" name="type" required />
                    </div>
                    <div class="form-group">
                        <label for="dept_name">Komenti</label>
                        <input type="text" class="form-control" value="{{ $leave->comment }}" placeholder="Shkruaj komentin" name="comment" />
                    </div>

                    <div class="form-group">
                        <label for="emp_id">Tipi i pushimit</label>
                        <select class="form-control" name="emp_id">
                            <option value="">Selekto</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="emp_id">Punëtori</label>
                        <select class="form-control" name="emp_id">
                            <option value="">Selekto</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dept_name">Data e fillimit</label>
                        <input type="date" class="form-control" value="{{ $leave->start_date }}" placeholder="Shtyp daten e fillimit" name="start_date" />
                    </div>

                    <div class="form-group">
                        <label for="dept_name">Data e mbarimit</label>
                        <input type="date" class="form-control" value="{{ $leave->end_date }}" placeholder="Shtyp daten e mbarimit" name="end_date" />
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
<div class="modal fade" id="delete{{ $leave->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header " style="align-items: center">
               
              <h4 class="modal-title "><span class="leave_id">Fshij Pushimin</span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('leaves.destroy', $leave->id) }}">
                    @csrf
                    {{ method_field('DELETE') }}
                    <div class="text-center">
                        <h6>A jeni i sigurt qe dëshironi ta fshini:</h6>
                        <h2 class="bold del_employee_name">{{$leave->type}} - {{ $leave->full_name }}</h2>
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
