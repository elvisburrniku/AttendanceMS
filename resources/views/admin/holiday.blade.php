@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Festat</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Fillimi</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Festat</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Lista e festave</a></li>
  
    </ol>
</div>
@endsection
@section('button')
<a href="#addnew" data-toggle="modal" class="btn btn-success btn-sm btn-flat"><i class="mdi mdi-plus mr-2"></i>Shto</a>
        

@endsection

@section('content')
@include('includes.flash')
<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                            <table id="datatable-buttons" class="table table-striped table-hover table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    
                                <thead class="thead-dark">
                                <tr>
                                    <th data-priority="1">Tipi</th>
                                    <th data-priority="2">Komenti</th>
                                    <th data-priority="5">Data</th>
                                    <th data-priority="6">Data e pushimit</th>
                                    <th data-priority="7">Veprimi</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach( $holidays as $holiday)
                                    <tr>
                                        <td>{{$holiday->type}}</td>
                                        <td>{{$holiday->comment }}</td>
                                        <td>{{ $holiday->date }}</td>
                                        <td>{{ $holiday->observedOn }}</td>
                                        <td>
    
                                            <a href="#edit{{$holiday->id}}" data-toggle="modal" class="btn btn-success btn-sm edit btn-flat"><i class='fa fa-edit'></i></a>
                                            <a href="#delete{{$holiday->id}}" data-toggle="modal" class="btn btn-danger btn-sm delete btn-flat"><i class='fa fa-trash'></i></a>
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
</div>
                                    
@foreach( $holidays as $holiday)
    @include('includes.edit_delete_holiday')
@endforeach

@include('includes.add_holiday')

@endsection
@section('script')
@endsection