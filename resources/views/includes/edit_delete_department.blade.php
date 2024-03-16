<!-- Edit -->
<div class="modal fade" id="edit{{ $department->dept_code }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Përditso Departamentin</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>
            <form class="form-horizontal" method="POST" action="{{ route('departments.update', $department->id) }}">
                <div class="modal-body text-left">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="emp_code">Kodi</label>
                        <input type="text" class="form-control" value="{{ $department->dept_code }}" placeholder="Shkruaj kodin e departamentit" id="dept_code" value="{{ $departments_count + 1 }}" name="dept_code"
                            required />
                    </div>
                    <div class="form-group">
                        <label for="dept_name">Emri</label>
                        <input type="text" class="form-control" value="{{ $department->dept_name }}" placeholder="Shkruaj emrin e departamentit" id="dept_name" name="dept_name"
                            required />
                    </div>

                    <div class="form-group">
                        <label for="parent_dept">Departamenti superior</label>
                        <select class="form-control" name="parent_dept" id="parent_dept">
                            <option value="">Selekto Departamentin</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                            @endforeach
                        </select>
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
<div class="modal fade" id="delete{{ $department->dept_code }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header " style="align-items: center">
               
              <h4 class="modal-title "><span class="department_id">Fshij Departamentin</span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('departments.destroy', $department->id) }}">
                    @csrf
                    {{ method_field('DELETE') }}
                    <div class="text-center">
                        <h6>A jeni i sigurt qe dëshironi ta fshini:</h6>
                        <h2 class="bold del_employee_name">{{$department->dept_name}}</h2>
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
