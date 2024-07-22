@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Pozicionet</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Fillimi</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Pozicionet</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Lista e pozicioneve</a></li>
  
    </ol>
</div>
@endsection
@section('button')
<a href="#addnew" data-toggle="modal" class="btn btn-success btn-sm btn-flat"><i class="mdi mdi-plus mr-2"></i>Shto pozicion të ri</a>
        

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
                                                        <th data-priority="3">Superior</th>
                                                        <th data-priority="7">Veprime</th>
                                                     
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach( $positions as $position)
                                                        <tr>
                                                            <td>{{$position->position_code}}</td>
                                                            <td>{{$position->position_name }}</td>
                                                            <td>{{ optional($position->parent_position)['position_name'] }}</td>
                                                            <td>
                        
                                                                <a href="#edit{{$position->position_code}}" data-toggle="modal" class="btn btn-success btn-sm edit btn-flat"><i class='fa fa-edit'></i></a>
                                                                <a href="#delete{{$position->position_code}}" data-toggle="modal" class="btn btn-danger btn-sm delete btn-flat"><i class='fa fa-trash'></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                   
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->    
                                    
@foreach( $positions as $position)
    @include('includes.edit_delete_position')
@endforeach

@include('includes.add_position')

@endsection
@section('script')

@endsection