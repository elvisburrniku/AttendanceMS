<!-- Edit -->
<div class="modal fade" id="edit{{ $holiday->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Përditso Festen</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>
            <form class="form-horizontal" method="POST" action="{{ route('holiday.update', $holiday->id) }}">
                <div class="modal-body text-left">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="emp_code">Tipi</label>
                        <input type="text" class="form-control" value="{{ $holiday->type }}" placeholder="Shkruaj tipin e pushimit" name="type" required />
                    </div>
                    <div class="form-group">
                        <label for="dept_name">Komenti</label>
                        <input type="text" class="form-control" value="{{ $holiday->comment }}" placeholder="Shkruaj komentin" name="comment" />
                    </div>

                    <div class="form-group">
                        <label for="dept_name">Data</label>
                        <input type="date" class="form-control" value="{{ $holiday->date }}" placeholder="Shtyp daten" name="date" required/>
                    </div>

                    <div class="form-group">
                        <label for="dept_name">Data e pushimit</label>
                        <input type="date" class="form-control" value="{{ $holiday->observed_on }}" placeholder="Shtyp daten e pushimit" name="observed_on" required/>
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
<div class="modal fade" id="delete{{ $holiday->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header " style="align-items: center">
               
              <h4 class="modal-title "><span class="leave_id">Fshij Festen</span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('holiday.destroy', $holiday->id) }}">
                    @csrf
                    {{ method_field('DELETE') }}
                    <div class="text-center">
                        <h6>A jeni i sigurt qe dëshironi ta fshini:</h6>
                        <h2 class="bold del_employee_name">{{$holiday->type}} - {{ $holiday->comment }}</h2>
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
