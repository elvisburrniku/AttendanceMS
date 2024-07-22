@extends('layouts.master')

@section('css')
    <!-- Table css -->
    <link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet"
        type="text/css" media="screen">
@endsection


@section('content')

    @php
        function convertDecimalToTime($decimalHours) {
            $hours = floor($decimalHours);
            $minutes = ($decimalHours - $hours) * 60;
            $minutesPart = floor($minutes);
            $seconds = ($minutes - $minutesPart) * 60;
            return sprintf("%02d:%02d:%02d", $hours, $minutesPart, round($seconds));
        }

        function calculateWorkedHours($decimalHours) {
            $hours = floor($decimalHours);
            $minutes = ($decimalHours - $hours) * 60;
            $roundedHours = $hours;
            if ($minutes >= 45) {
                $roundedHours++;
            }
            return $roundedHours;
        }
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive table-hover table-bordered table-sm">
                <thead class="thead-dark">
                        <tr>

                            <th>Punëtori</th>
                            <th>Departmenti</th>
                            <!-- <th>ID</th> -->
                            @php
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
                           
                            <button type="submit" class="btn btn-success" style="display: flex; margin:10px">Submit Overtime {{ $today->startOfMonth()->toDateString() }} - {{ $today->endOfMonth()->toDateString() }}</button>
                            <div class="d-flex justify-end float-right">
                                <label class="d-flex align-items-center">
                                    <span class="mr-1 font-weight-normal">Data:</span><input type="month" id="month" value="{{ request()->month ?? now()->format('Y-m-d') }}" class="form-control form-control-sm" placeholder="Selekto muajin" aria-controls="datatable-buttons">
                                </label>
                            </div>
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

                                            $hours = calculateWorkedHours(optional($check_attd)->total_hr ?? 0);
                                        @endphp
                                        <td>
                                            <div class="d-flex">
                                                <div class="mr-1" @if($hours == 0) style="color: red;" @endif>{{ $hours }}</div>
                                                <div class="form-check form-check-inline">
                                                    <input type="hidden" name="attd[{{ $date_picker }}][{{ $employee->id }}][total_hr]" value="{{ $hours }}">
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
@section('script-bottom')
    <script>
        $(function() {
            $('#month').on('change', function(e) {

                var selectedDate = $(this).val();
    
                // Get the current URL without query parameters
                var baseUrl = window.location.origin + window.location.pathname;

                // Construct the new URL with the updated query parameter
                var newUrl = baseUrl + '?month=' + encodeURIComponent(selectedDate);

                // Redirect to the new URL
                window.location.href = newUrl;
            });
        });
    </script>
@endsection




