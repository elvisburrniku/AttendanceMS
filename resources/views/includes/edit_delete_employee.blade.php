<!-- Edit -->
<div class="modal fade" id="edit{{ $employee->emp_code }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"><b>Përditso puntorin</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>
            <div class="modal-body text-left">
                <form class="form-horizontal" method="POST" action="{{ route('employees.update', $employee->id) }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="emp_code">Code</label>
                        <input type="text" class="form-control" placeholder="Enter a Employee code" id="emp_code" value="{{ $employee->emp_code }}" name="emp_code"
                            required />
                    </div>
                    <div class="form-group">
                        <label for="first_name">First name</label>
                        <input type="text" class="form-control" placeholder="Enter a Employee name" id="first_name" value="{{ $employee->first_name }}" name="first_name"
                            required />
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last name</label>
                        <input type="text" class="form-control" placeholder="Enter a Employee last name" id="last_name" value="{{ $employee->last_name }}" name="last_name"
                            required />
                    </div>
                    <div class="form-group">
                        <label for="nickname">Nickname</label>
                        <input type="text" class="form-control" placeholder="Enter Employe nickname" id="nickname" value="{{ $employee->nickname }}" name="nickname"/>
                    </div>

                    <div class="form-group">
                        <label for="card_no">Card no</label>
                        <input type="text" class="form-control" placeholder="Enter Employe Card number" id="card_no" value="{{ $employee->card_no }}" name="card_no"/>
                    </div>

                    <div class="form-group">
                        <label for="department">Department</label>
                        <select required class="form-control" name="department" id="department">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                @if(is_countable($employee->department) && count($employee->department) > 0)
                                    <option {{ $employee->department['id'] == $department->id ? 'selected' : '' }} value="{{ $department->id }}">{{ $department->dept_name }}</option>
                                @else
                                    <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="position">Position</label>
                        <select required class="form-control" name="position" id="position">
                            <option value="">Select Position</option>
                            @foreach($positions as $position)
                                @if(is_countable($employee->position) && count($employee->position) > 0)
                                    <option {{ $employee->position['id'] == $position->id ? 'selected' : '' }} value="{{ $position->id }}">{{ $position->position_name }}</option>
                                @else
                                    <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="hide_date">Hire Date</label>
                        <input required type="date" class="form-control" value="{{ now()->format('Y-m-d') }}" placeholder="Enter Employe Hire date" id="hire_date" value="{{ $employee->hire_date }}" name="hire_date"/>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select required class="form-control" name="gender" id="gender">
                            <option value="">Select Gender</option>
                            @foreach(['M' => 'Male', 'F' => 'Female', 'O' => 'Other'] as $id => $gender)
                                <option {{ $employee->gender == $id ? 'selected' : '' }} value="{{ $id }}">{{ $gender }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="birthday">Birthday</label>
                        <input required type="date" class="form-control" value="{{ now()->format('Y-m-d') }}" placeholder="Enter Employe Birthday" id="birthday" value="{{ $employee->birthday }}" name="birthday"/>
                    </div>

                    <div class="form-group">
                        <label for="emp_type">Employee Type</label>
                        <select required class="form-control" name="emp_type" id="emp_type">
                            <option value="">Select Employee Type</option>
                            @foreach(['1' => 'Official', '2' => 'Temporary', '3' => 'Probation'] as $id => $emp_type)
                                <option {{ $employee->emp_type == $id ? 'selected' : '' }} value="{{ $id }}">{{ $emp_type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contact_tel">Contact tel</label>
                        <input type="text" class="form-control" placeholder="Enter Employe Contact tel" id="contact_tel" value="{{ $employee->contact_tel }}" name="contact_tel"/>
                    </div>

                    <div class="form-group">
                        <label for="office_tel">Official tel</label>
                        <input type="text" class="form-control" placeholder="Enter Employe Official tel" id="office_tel" value="{{ $employee->office_tel }}" name="office_tel"/>
                    </div>

                    <div class="form-group">
                        <label for="mobile">Mobile</label>
                        <input type="text" class="form-control" placeholder="Enter Employe Mobile" id="mobile" value="{{ $employee->mobile }}" name="mobile"/>
                    </div>
                    
                    <div class="form-group">
                        <label for="national">National</label>
                        <input type="text" class="form-control" placeholder="Enter Employe National" id="national" value="{{ $employee->national }}" name="national"/>
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" placeholder="Enter Employe City" id="city" value="{{ $employee->city }}" name="city"/>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" placeholder="Enter Employe Address" id="address" value="{{ $employee->address }}" name="address"/>
                    </div>

                    <div class="form-group">
                        <label for="postcode">Postcode</label>
                        <input type="text" class="form-control" placeholder="Enter Employe Postcode" id="postcode" value="{{ $employee->postcode }}" name="postcode"/>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <input type="email" class="form-control" placeholder="Enter Employe Email" id="email" value="{{ $employee->email }}" name="email">
                    </div>

                    <div class="form-group">
                    <label for="dev_privilege">Device Privilege</label>
                        <select required class="form-control" name="dev_privilege" id="dev_privilege">
                            <option value="">Select Device Privilege</option>
                            @foreach(['0' => 'Employee', '1' => 'Register', '6' => 'System Administrator', '10' => 'User Defined', '14' => 'Super Administrator'] as $id => $dev_privilege)
                                <option {{ $employee->dev_privilege == $id ? 'selected' : '' }} value="{{ $id }}">{{ $dev_privilege }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="area">Area</label>
                        <select required multiple class="form-control" name="area[]" id="area">
                            <option value="">Select Area</option>
                            @foreach($areas as $area)
                                @if(is_countable($employee->area) && count($employee->area) > 0)
                                    <option {{ collect($employee->area)->where('id', $area->id)->first() ? 'selected' : '' }} value="{{ $area->id }}">{{ $area->area_name }}</option>
                                @else
                                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="app_status">App Status</label>
                        <select required class="form-control" name="app_status" id="app_status">
                            <option value="">Select App Status</option>
                            @foreach(['1' => 'Enabled', '0' => 'Disabled'] as $id => $app_status)
                                <option {{ $employee->app_status == $id ? 'selected' : '' }} value="{{ $id }}">{{ $app_status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="app_role">App Role</label>
                        <select required class="form-control" name="app_role" id="app_role">
                            <option value="">Select App Role</option>
                            @foreach(['1' => 'Employee', '2' => 'Administrator'] as $id => $app_role)
                                <option {{ $employee->app_role == $id ? 'selected' : '' }} value="{{ $id }}">{{ $app_role }}</option>
                            @endforeach
                        </select>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i
                        class="fa fa-close"></i> Anulo</button>
                <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i>
                    Përditso</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete -->
<div class="modal fade" id="delete{{ $employee->emp_code }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header " style="align-items: center">
               
              <h4 class="modal-title "><span class="employee_id">Delete Employee</span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('employees.destroy', $employee->id) }}">
                    @csrf
                    {{ method_field('DELETE') }}
                    <div class="text-center">
                        <h6>Are you sure you want to delete:</h6>
                        <h2 class="bold del_employee_name">{{$employee->first_name}}</h2>
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
