<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Shto Zonë</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>

            
            <div class="modal-body">
                <div class="card-body text-left">
                    <form method="POST" action="{{ route('areas.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="emp_code">Kodi</label>
                            <input type="text" class="form-control" placeholder="Shkruaj kodin e zonës" id="area_code" value="{{ $areas_count + 1 }}" name="area_code"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="area_name">Emri</label>
                            <input type="text" class="form-control" placeholder="Shkruaj emrin e zonës" id="area_name" name="area_name"
                                required />
                        </div>

                        <div class="form-group">
                            <label for="parent_area">Zona superior</label>
                            <select class="form-control" name="parent_area" id="parent_area">
                                <option value="">Selekto Zonën</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
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