@extends('layouts.master')

@section('css')
    <!-- Table css -->
    <link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet"
        type="text/css" media="screen">
@endsection


@section('content')

    <div class="card">
	<!-- Log on to codeastro.com for more projects! -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive table-hover table-bordered table-sm">
                <thead class="thead-dark">
                        <tr>

                            <th>Puntori</th>
                            <th>Departmenti</th>
                            <!-- <th>ID</th> -->
							<!-- Log on to codeastro.com for more projects! -->
                            @php
                                $today = today();
                                $dates = [];
                                
                                for ($i = 1; $i < $today->daysInMonth + 1; ++$i) {
                                    $dates[] = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
                                }
                                
                            @endphp
                            @foreach ($dates as $key => $date)
                                <th>
                                    {{ $key + 1 }}
                                </th>

                            @endforeach

                            <th style="background: #35dc35; border-color: #35dc35;"> Total </th>

                        </tr>
                    </thead>

                    <tbody>


                        <form action="{{ route('overtime_store') }}" method="post">
                           
                            <button type="submit" class="btn btn-success" style="display: flex; margin:10px">Submit Overtime {{ now()->startOfMonth()->toDateString() }} - {{ now()->endOfMonth()->toDateString() }}</button>
                            @csrf
                            @foreach ($employees as $employee)

                                <input type="hidden" name="emp_id" value="{{ $employee->id }}">

                                <tr>
                                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                    <td>{{ $employee->department->dept_name }}</td>
                                    @php
                                        $total = 0;
                                    @endphp
                                    @for ($i = 1; $i < $today->daysInMonth + 1; ++$i)


                                        @php
                                            $date_picker = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
                                            $check_attd = $employee->getTotalOvertimeByDate($date_picker);
                                        @endphp
                                        <td>
                                            <div class="d-flex">
                                                <div class="mr-1" @if(optional($check_attd)->total_hr && optional($check_attd)->total_hr == 0) style="color: red;" @endif>{{ optional($check_attd)->total_hr ?? 0 }}</div>
                                                <div class="form-check form-check-inline">
                                                    <input type="hidden" name="attd[{{ $date_picker }}][{{ $employee->id }}][total_hr]" value="{{ optional($check_attd)->total_hr }}">
                                                    <input class="form-check-input" id="check_box-{{ $date_picker }}-{{ $employee->id }}"
                                                        name="attd[{{ $date_picker }}][{{ $employee->id }}][approved]" type="checkbox"
                                                        @if(optional($check_attd)->approved)  checked @endif value="1">

                                                </div>
                                            </div>

                                        </td>
                                     
                                    @endfor

                                    @php
                                        $hours_total = $employee->overtimes->sum('total_hr');
                                    @endphp

                                    <td  style="@if($hours_total == 0) color: red; @endif background: #c7fcc7; border-color: #c7fcc7;">
                                        {{ $hours_total }}
                                    </td>

                                </tr>
                            @endforeach

                        </form>


                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection




