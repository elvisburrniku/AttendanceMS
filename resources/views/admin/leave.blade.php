@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Pushimet</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Fillimi</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Pushimet</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Lista e pushimeve</a></li>
  
    </ol>
</div>
@endsection
@section('button')
<a href="#addnew" data-toggle="modal" class="btn btn-success btn-sm btn-flat"><i class="mdi mdi-plus mr-2"></i>Shto Pushim</a>
        

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
                                                        <th data-priority="1">Tipi</th>
                                                        <th data-priority="2">Komenti</th>
                                                        <th data-priority="3">Lloji i pushimit</th>
                                                        <th data-priority="4">Punëtori</th>
                                                        <th data-priority="5">Data e fillimit</th>
                                                        <th data-priority="6">Data e mbarimit</th>
                                                        <th data-priority="6">Totali i ditëve</th>
                                                        <th data-priority="7">Veprimi</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach( $leaves as $leave)
                                                        <tr>
                                                            <td>{{$leave->type}}</td>
                                                            <td>{{$leave->comment }}</td>
                                                            <td>{{ optional($leave->leaveType)->name }}</td>
                                                            <td>{{ $leave->full_name }}</td>
                                                            <td>{{ $leave->start_date }}</td>
                                                            <td>{{ $leave->end_date }}</td>
                                                            <td>{{ $leave->countLeaveDays() }}</td>
                                                            <td>
                        
                                                                <a href="#edit{{$leave->id}}" data-toggle="modal" class="btn btn-success btn-sm edit btn-flat"><i class='fa fa-edit'></i></a>
                                                                <a href="#delete{{$leave->id}}" data-toggle="modal" class="btn btn-danger btn-sm delete btn-flat"><i class='fa fa-trash'></i></a>
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
                                    
@foreach( $leaves as $leave)
    @include('includes.edit_delete_leave')
@endforeach

@include('includes.add_leave')

@endsection
@section('script')

@endsection