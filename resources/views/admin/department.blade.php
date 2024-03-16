@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Departamentet</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Departamentet</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Lista e departamentet</a></li>
  
    </ol>
</div>
@endsection
@section('button')
<a href="#addnew" data-toggle="modal" class="btn btn-success btn-sm btn-flat"><i class="mdi mdi-plus mr-2"></i>Shto departament të ri</a>
        

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
                                                        <th data-priority="1">Kodi</th>
                                                        <th data-priority="2">Emri</th>
                                                        <th data-priority="3">Superior</th>
                                                        <th data-priority="7">Veprime</th>
                                                     
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach( $departments as $department)
                                                        <tr>
                                                            <td>{{$department->dept_code}}</td>
                                                            <td>{{$department->dept_name }}</td>
                                                            <td>{{ optional($department->parent_dept)['dept_name'] }}</td>
                                                            <td>
                        
                                                                <a href="#edit{{$department->dept_code}}" data-toggle="modal" class="btn btn-success btn-sm edit btn-flat"><i class='fa fa-edit'></i></a>
                                                                <a href="#delete{{$department->dept_code}}" data-toggle="modal" class="btn btn-danger btn-sm delete btn-flat"><i class='fa fa-trash'></i></a>
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
                                    
@foreach( $departments as $department)
@include('includes.edit_delete_department')
@endforeach

@include('includes.add_department')

@endsection


@section('script')
<!-- Responsive-table-->

@endsection