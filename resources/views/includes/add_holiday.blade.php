<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Shto Festë</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>

            
            <div class="modal-body">
                <div class="card-body text-left">
                    <form method="POST" action="{{ route('holiday.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="emp_code">Tipi</label>
                            <input type="text" class="form-control" placeholder="Shkruaj tipin e pushimit" name="type" required />
                        </div>
                        <div class="form-group">
                            <label for="dept_name">Komenti</label>
                            <input type="text" class="form-control" placeholder="Shkruaj komentin" name="comment" />
                        </div>

                        <div class="form-group">
                            <label for="dept_name">Data</label>
                            <input type="date" class="form-control" placeholder="Shtyp daten " name="date" required/>
                        </div>

                        <div class="form-group">
                            <label for="dept_name">Data e pushimit</label>
                            <input type="date" class="form-control" placeholder="Shtyp daten e pushimit" name="observed_on" required/>
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

        </div>

    </div>
</div>
</div>