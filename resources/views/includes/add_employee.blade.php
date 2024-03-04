<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
			<!-- Log on to codeastro.com for more projects! -->
        
            <div class="modal-header">
            <h5 class="modal-title"><b>Add New Employee</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>

            
            <div class="modal-body">
			<!-- Log on to codeastro.com for more projects! -->

                <div class="card-body text-left">

                    <form method="POST" action="{{ route('employees.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="emp_code">Code</label>
                            <input type="text" class="form-control" placeholder="Enter a Employee code" id="emp_code" value="{{ $employee_count + 1 }}" name="emp_code"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="first_name">First name</label>
                            <input type="text" class="form-control" placeholder="Enter a Employee name" id="first_name" name="first_name"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last name</label>
                            <input type="text" class="form-control" placeholder="Enter a Employee last name" id="last_name" name="last_name"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="nickname">Nickname</label>
                            <input type="text" class="form-control" placeholder="Enter Employe nickname" id="nickname" name="nickname"/>
                        </div>

                        <div class="form-group">
                            <label for="card_no">Card no</label>
                            <input type="text" class="form-control" placeholder="Enter Employe Card number" id="card_no" name="card_no"/>
                        </div>

                        <div class="form-group">
                            <label for="department">Department</label>
                            <select required class="form-control" name="department" id="department">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="position">Position</label>
                            <select required class="form-control" name="position" id="position">
                                <option value="">Select Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="hide_date">Hire Date</label>
                            <input required type="date" class="form-control" value="{{ now()->format('Y-m-d') }}" placeholder="Enter Employe Hire date" id="hide_date" name="hide_date"/>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select required class="form-control" name="gender" id="gender">
                                <option value="">Select Gender</option>
                                @foreach(['M' => 'Male', 'F' => 'Female', 'O' => 'Other'] as $id => $gender)
                                    <option value="{{ $id }}">{{ $gender }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="birthday">Birthday</label>
                            <input required type="date" class="form-control" value="{{ now()->format('Y-m-d') }}" placeholder="Enter Employe Birthday" id="birthday" name="birthday"/>
                        </div>

                        <div class="form-group">
                            <label for="emp_type">Employee Type</label>
                            <select required class="form-control" name="emp_type" id="emp_type">
                                <option value="">Select Employee Type</option>
                                @foreach(['1' => 'Official', '2' => 'Temporary', '3' => 'Probation'] as $id => $emp_type)
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
                            <label for="mobile">Official tel</label>
                            <input type="text" class="form-control" placeholder="Enter Employe Mobile" id="mobile" name="mobile"/>
                        </div>
                        
                        <div class="form-group">
                            <label for="national">National</label>
                            <input type="text" class="form-control" placeholder="Enter Employe National" id="national" name="national"/>
                        </div>

                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" class="form-control" placeholder="Enter Employe City" id="city" name="city"/>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" placeholder="Enter Employe Address" id="address" name="address"/>
                        </div>

                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" class="form-control" placeholder="Enter Employe Postcode" id="postcode" name="postcode"/>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label">Email</label>
                            <input type="email" class="form-control" placeholder="Enter Employe Email" id="email" name="email">
                        </div>

                        <div class="form-group">
                        <label for="dev_privilege">Device Privilege</label>
                            <select required class="form-control" name="dev_privilege" id="dev_privilege">
                                <option value="">Select Device Privilege</option>
                                @foreach(['0' => 'Employee', '1' => 'Register', '6' => 'System Administrator', '10' => 'User Defined', '14' => 'Super Administrator'] as $id => $dev_privilege)
                                    <option value="{{ $id }}">{{ $dev_privilege }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="area">Area</label>
                            <select required multiple class="form-control" name="area[]" id="area">
                                <option value="">Select Area</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="app_status">App Status</label>
                            <select required class="form-control" name="app_status" id="app_status">
                                <option value="">Select App Status</option>
                                @foreach(['1' => 'Enabled', '0' => 'Disabled'] as $id => $app_status)
                                    <option value="{{ $id }}">{{ $app_status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="app_role">App Status</label>
                            <select required class="form-control" name="app_role" id="app_role">
                                <option value="">Select App Status</option>
                                @foreach(['1' => 'Employee', '2' => 'Administrator'] as $id => $app_role)
                                    <option value="{{ $id }}">{{ $app_role }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <div>
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    Submit
                                </button>
                                <button type="reset" class="btn btn-danger waves-effect m-l-5" data-dismiss="modal">
                                    Cancel
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