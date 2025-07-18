<!-- Edit -->
<div class="modal fade" id="edit{{ $area->area_code }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Përditso zonën</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>
            <form class="form-horizontal" method="POST" action="{{ route('areas.update', $area->id) }}">
                <div class="modal-body text-left">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="emp_code">Kodi</label>
                        <input type="text" class="form-control" value="{{ $area->area_code }}" placeholder="Shkruaj kodin e zonës" id="area_code" value="{{ $areas_count + 1 }}" name="area_code"
                            required />
                    </div>
                    <div class="form-group">
                        <label for="area_name">Emri</label>
                        <input type="text" class="form-control" value="{{ $area->area_name }}" placeholder="Shkruaj emrin e zonës" id="area_name" name="area_name"
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
<div class="modal fade" id="delete{{ $area->area_code }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header " style="align-items: center">
               
              <h4 class="modal-title "><span class="position_id">Fshij Zonën</span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('areas.destroy', $area->id) }}">
                    @csrf
                    {{ method_field('DELETE') }}
                    <div class="text-center">
                        <h6>A jeni i sigurt qe dëshironi ta fshini:</h6>
                        <h2 class="bold del_employee_name">{{$area->area_name}}</h2>
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
