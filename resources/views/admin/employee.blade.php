@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Employees</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Employees</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Employees List</a></li>
  
    </ol>
</div>
@endsection
@section('button')
<a href="#addnew" data-toggle="modal" class="btn btn-success btn-sm btn-flat"><i class="mdi mdi-plus mr-2"></i>Add New Employee</a>
        

@endsection

@section('content')
@include('includes.flash')


                      <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
									<!-- Log on to codeastro.com for more projects! -->
                                                <table id="datatable-buttons" class="table table-striped table-hover table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th data-priority="1">ID</th>
                                                        <th data-priority="2">Name</th>
                                                        <th data-priority="3">Area</th>
                                                        <th data-priority="4">Email</th>
                                                        <th data-priority="5">Department</th>
                                                        <th data-priority="6">Member Since</th>
                                                        <th data-priority="7">Actions</th>
                                                     
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach( $employees as $employee)

                                                        <tr>
                                                            <td>{{$employee->id}}</td>
                                                            <td>{{$employee->first_name}} {{ $employee->last_name }}</td>
                                                            <td>{{$employee->area[0]['area_name']}}</td>
                                                            <td>{{$employee->email}}</td>
                                                            <td>
                                                                {{$employee->department['dept_name']}}
                                                            </td>
                                                            <td>{{$employee->hire_date}}</td>
                                                            <td>
                        
                                                                <a href="#edit{{$employee->first_name}}" data-toggle="modal" class="btn btn-success btn-sm edit btn-flat"><i class='fa fa-edit'></i></a>
                                                                <a href="#delete{{$employee->first_name}}" data-toggle="modal" class="btn btn-danger btn-sm delete btn-flat"><i class='fa fa-trash'></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                   
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
									<!-- Log on to codeastro.com for more projects! -->
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->    
                                    
@if(false)
@foreach( $employees as $employee)
@include('includes.edit_delete_employee')
@endforeach
@endif

@include('includes.add_employee')

@endsection


@section('script')
<!-- Responsive-table-->

@endsection