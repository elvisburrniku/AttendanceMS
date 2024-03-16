<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Shto Departament</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>

            
            <div class="modal-body">
                <div class="card-body text-left">
                    <form method="POST" action="{{ route('departments.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="emp_code">Kodi</label>
                            <input type="text" class="form-control" placeholder="Shkruaj kodin e departamentit" id="dept_code" value="{{ $departments_count + 1 }}" name="dept_code"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="dept_name">Emri</label>
                            <input type="text" class="form-control" placeholder="Shkruaj emrin e departamentit" id="dept_name" name="dept_name"
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
                        
                        <div class="form-group">
                            <div>
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    Ruaj
                                </button>
                                <button type="reset" class="btn btn-danger waves-effect m-l-5" data-dismiss="modal">
                                    Anulo
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
			<!-- Log on to codeastro.com for more projects! -->

        </div>

    </div>
</div>
</div>