<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Shto Pozicion</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>

            
            <div class="modal-body">
                <div class="card-body text-left">
                    <form method="POST" action="{{ route('positions.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="emp_code">Kodi</label>
                            <input type="text" class="form-control" placeholder="Shkruaj kodin e pozicionit" id="position_code" value="{{ $positions_count + 1 }}" name="position_code"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="position_name">Emri</label>
                            <input type="text" class="form-control" placeholder="Shkruaj emrin e pozicionit" id="position_name" name="position_name"
                                required />
                        </div>

                        <div class="form-group">
                            <label for="parent_position">Pozicioni superior</label>
                            <select class="form-control" name="parent_position" id="parent_position">
                                <option value="">Selekto Pozicioni</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->position_name }}</option>
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

        </div>

    </div>
</div>
</div>