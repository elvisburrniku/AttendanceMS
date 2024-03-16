@extends('layouts.master')

@section('css')
    <!-- Table css -->
    <link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@endsection

@section('breadcrumb')
    <div class="col-sm-6">
        <h4 class="page-title text-left">CheckIn/Out</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Fillimi</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">CheckIn/Out</a></li>


        </ol>
    </div>
@endsection
@section('button')
    <a href="/attendances/export" class="btn btn-success btn-sm btn-flat"><i class="mdi mdi-plus mr-2"></i>Export</a>
@endsection

@section('content')
@include('includes.flash')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="datatable-buttons" class="table table-hover table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        
                            <thead class="thead-dark">
                                    <tr>
                                        <th data-priority="1">Emri</th>
                                        <th data-priority="2">Data</th>
                                        <th data-priority="3">Dita</th>
                                        <th data-priority="4">Hyrje</th>
                                        <th data-priority="5">Pauza</th>
                                        <th data-priority="6">Dalje</th>
                                        <th data-priority="7">Koha</th>
                                        <th data-priority="8">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($attendances as $attendance)

                                        <tr>
                                            <td>{{ $attendance->first_name }} {{ $attendance->last_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->upload_time)->toDateString() }}</td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->upload_time)->format('l') }}</td>
                                            <td>
                                                {!! $attendance->checkin_time !!}
                                            </td>
                                            <td>
                                                {{ $attendance->break_in_time}} - {{ $attendance->break_out_time}}
                                            </td>
                                            <td>
                                                {!! $attendance->checkout_time !!}
                                            </td>
                                            <td>{{ $attendance->difference }} </td>
                                            <td>
                                                <a href="#delete{{$attendance->id}}" data-toggle="modal" class="btn btn-danger btn-sm delete btn-flat"><i class='fa fa-trash'></i></a>
                                            </td>
                                        </tr>

                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!-- Log on to codeastro.com for more projects! -->
        </div> <!-- end col -->
    </div> <!-- end row -->

@foreach( $attendances as $attendance)
    @include('includes.edit_delete_attendances')
@endforeach

@endsection


@section('script')
    <!-- Responsive-table-->
	<!-- Log on to codeastro.com for more projects! -->
    <script src="{{ URL::asset('plugins/RWD-Table-Patterns/dist/js/rwd-table.min.js') }}"></script>
 
@endsection

@section('script')
    <script>
        $(function() {
            $('.table-responsive').responsiveTable({
                addDisplayAllBtn: 'btn btn-secondary'
            });
        });
    </script>
@endsection
