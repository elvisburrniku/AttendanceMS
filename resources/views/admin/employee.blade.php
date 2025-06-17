@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Punonjësit</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Punonjësit</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Lista e punonjësve</a></li>
  
    </ol>
</div>
@endsection
@section('button')
<a href="#addnew" data-toggle="modal" class="btn btn-success btn-sm btn-flat"><i class="mdi mdi-plus mr-2"></i>Shto punëtor të ri</a>
        

@endsection

@section('content')
@include('includes.flash')


                      <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table id="datatable-buttons" class="table table-striped table-hover table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                
                                            <thead class="thead-dark">
                                            <tr>
                                                <th data-priority="1">Kodi</th>
                                                <th data-priority="2">Emri</th>
                                                <th data-priority="3">Area</th>
                                                <th data-priority="4">Email</th>
                                                <th data-priority="5">Departamenti</th>
                                                <th data-priority="6">I punësuar nga</th>
                                                <th data-priority="7">Veprime</th>
                                                
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach( $employees as $employee)
                                                <tr>
                                                    <td>{{$employee->emp_code}}</td>
                                                    <td>{{$employee->first_name}} {{ $employee->last_name }}</td>
                                                    <td>{{ optional($employee->areas->first())->area_name ?? 'N/A' }}</td>
                                                    <td>{{$employee->email}}</td>
                                                    <td>
                                                        {{$employee->department->dept_name }}
                                                    </td>
                                                    <td>{{$employee->hire_date}}</td>
                                                    <td>
                
                                                        <a href="#edit{{$employee->emp_code}}" data-toggle="modal" class="btn btn-success btn-sm edit btn-flat"><i class='fa fa-edit'></i></a>
                                                        <a href="#delete{{$employee->emp_code}}" data-toggle="modal" class="btn btn-danger btn-sm delete btn-flat"><i class='fa fa-trash'></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            
                                            </tbody>
                                        </table>

                                        <div class="float-right">
                                            {{ $employees->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->    
                                    
@foreach( $employees as $employee)
@include('includes.edit_delete_employee')
@endforeach

@include('includes.add_employee')

@endsection


@section('script')
<!-- Responsive-table-->

@endsection