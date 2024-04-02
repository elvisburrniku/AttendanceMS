@extends('layouts.master')

@section('css')
    <!-- Table css -->
    <link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet"
        type="text/css" media="screen">
@endsection

@section('breadcrumb')
    <div class="col-sm-6">
        <h4 class="page-title text-left">Schedules</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Schedule</a></li>
			<!-- Log on to codeastro.com for more projects! -->
 

        </ol>
    </div>
@endsection
@section('button')
    <a href="#addnew" data-toggle="modal" class="btn btn-success btn-sm btn-flat"><i class="mdi mdi-plus mr-2"></i>Add New Schedule</a>


@endsection

@section('content')
@include('includes.flash')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
				<!-- Log on to codeastro.com for more projects! -->

                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="datatable-buttons" class="table table-hover table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        
                            <thead class="thead-dark">
                                    <tr>
                                        <th data-priority="1">#</th>
                                        <th data-priority="2">Name</th>
                                        <th data-priority="2">Type</th>
                                        <th data-priority="3">Check In</th>
                                        <th data-priority="4">Check Out</th>
                                        <th data-priority="4">Worktime</th>
                                        <th data-priority="5">Actions</th>
                                     

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($timetables as $timetable)
                                        <tr>
                                            <td> {{ $timetable->id }} </td>
                                            <td> {{ $timetable->alias }} </td>
                                            <td> {{ $timetable->type }} </td>
                                            @php
                                                $now = \Carbon\Carbon::parse(now()->format('Y-m-d').' '.$timetable->in_time);
                                                $in_time = $now->format('H:i');
                                                $out_time = $now->addMinutes($timetable->duration);
                                            @endphp
                                            <td> {{ $in_time }} </td>
                                            <td> {{ $out_time->format('H:i') }} </td>
                                            <td> {{ $timetable->duration }} min</td>
                                            <td>

                                                <a href="#delete{{ $timetable->id }}" data-toggle="modal"
                                                    class="btn btn-danger btn-sm delete btn-flat"><i
                                                        class='fa fa-trash'></i></a>

                                            </td>
                                        </tr>
                                    @endforeach


                                </tbody>
								<!-- Log on to codeastro.com for more projects! -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->


@endsection


@section('script')
    <!-- Responsive-table-->
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
