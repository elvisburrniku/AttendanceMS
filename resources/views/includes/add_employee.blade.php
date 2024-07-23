<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
        
            <div class="modal-header">
            <h5 class="modal-title"><b>Shto punëtor të ri</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>

            
            <div class="modal-body">

                <div class="card-body text-left">

                    <form method="POST" action="{{ route('employees.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="emp_code">Shifra</label>
                            <input type="text" class="form-control" placeholder="Shkruaj shifrën" id="emp_code" value="{{ $employee_count + 1 }}" name="emp_code"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="first_name">Emri</label>
                            <input type="text" class="form-control" placeholder="Shkruaj emrin" id="first_name" name="first_name"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="last_name">Mbiemri</label>
                            <input type="text" class="form-control" placeholder="Shkruaj mbiemrin" id="last_name" name="last_name"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="nickname">Nickname</label>
                            <input type="text" class="form-control" placeholder="Shkruaj emrin që shfaqet në paisje" id="nickname" name="nickname"/>
                        </div>

                        <div class="form-group">
                            <label for="card_no">Numri i kartelës</label>
                            <input type="text" class="form-control" placeholder="Shkruaj numrin e karteles" id="card_no" name="card_no"/>
                        </div>

                        <div class="form-group">
                            <label for="department">Departamenti</label>
                            <select required class="form-control" name="department" id="department">
                                <option value="">Selekto</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="position">Pozita</label>
                            <select required class="form-control" name="position" id="position">
                                <option value="">Selekto Pozitën</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="hire_date">Data e fillimit</label>
                            <input required type="date" class="form-control" value="{{ now()->format('Y-m-d') }}" placeholder="Shkruaj datën e fillimit të punës" id="hire_date" name="hire_date"/>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gjinia</label>
                            <select required class="form-control" name="gender" id="gender">
                                <option value="">Selekto gjininë</option>
                                @foreach(['M' => 'Mashkull', 'F' => 'Femër', 'O' => 'Tjetër'] as $id => $gender)
                                    <option value="{{ $id }}">{{ $gender }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="birthday">Data e lindjes</label>
                            <input required type="date" class="form-control" value="{{ now()->format('Y-m-d') }}" placeholder="Shkruaj datën e lindjes" id="birthday" name="birthday"/>
                        </div>

                        <div class="form-group">
                            <label for="emp_type">Lloji i punësimit</label>
                            <select required class="form-control" name="emp_type" id="emp_type">
                                <option value="">Selekto</option>
                                @foreach(['1' => 'Zyrtar', '2' => 'I përkohshëm', '3' => 'Provues'] as $id => $emp_type)
                                    <option value="{{ $id }}">{{ $emp_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_tel">Contact tel</label>
                            <input type="text" class="form-control" placeholder="Enter Employe Contact tel" id="contact_tel" name="contact_tel"/>
                        </div>

                        <div class="form-group">
                            <label for="office_tel">Official tel</label>
                            <input type="text" class="form-control" placeholder="Enter Employe Official tel" id="office_tel" name="office_tel"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="mobile">Telefoni</label>
                            <input type="text" class="form-control" placeholder="Shkruaj numrin e telefonit" id="mobile" name="mobile"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="national">Nacionaliteti</label>
                            <input type="text" class="form-control" placeholder="Shkruaj nacionalitetin" id="national" name="national"/>
                        </div>

                        <div class="form-group">
                            <label for="city">Qyteti</label>
                            <input type="text" class="form-control" placeholder="Shkruaj qytetin" id="city" name="city"/>
                        </div>

                        <div class="form-group">
                            <label for="address">Adresa</label>
                            <input type="text" class="form-control" placeholder="Shkruaj adresen" id="address" name="address"/>
                        </div>

                        <div class="form-group">
                            <label for="postcode">Kodi Postar</label>
                            <input type="text" class="form-control" placeholder="Shkruaj kodin postar" id="postcode" name="postcode"/>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label">Email</label>
                            <input type="email" class="form-control" placeholder="Shkruaj email adresën" id="email" name="email">
                        </div>

                        <div class="form-group">
                        <label for="dev_privilege">Privilegjet në paisje</label>
                            <select required class="form-control" name="dev_privilege" id="dev_privilege">
                                <option value="">Selekto</option>
                                @foreach(['0' => 'Punonjës', '1' => 'Register', '6' => 'System Administrator', '10' => 'User Defined', '14' => 'Super Administrator'] as $id => $dev_privilege)
                                    <option value="{{ $id }}">{{ $dev_privilege }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="area">Zona</label>
                            <select required multiple class="form-control" name="area[]" id="area">
                                <option value="">Selekto</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="app_status">App Status</label>
                            <select required class="form-control" name="app_status" id="app_status">
                                <option value="">Selekt</option>
                                @foreach(['1' => 'Lejohet', '0' => 'Nuk lejohet'] as $id => $app_status)
                                    <option value="{{ $id }}">{{ $app_status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="app_role">Roli në APP</label>
                            <select required class="form-control" name="app_role" id="app_role">
                                <option value="">Selekt</option>
                                @foreach(['1' => 'Punonjës', '2' => 'Administrator'] as $id => $app_role)
                                    <option value="{{ $id }}">{{ $app_role }}</option>
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