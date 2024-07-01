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
                            <th style="background: #35dc35; border-color: #35dc35;"> Or pune </th>
                            <th style="background: #35dc35; border-color: #35dc35;"> Overtime </th>
                            <th style="background: #35dc35; border-color: #35dc35;"> Total </th>
                        </tr>
                    </thead>

                    <tbody>


                        <form action="{{ route('check_store') }}" method="post">
                           
                            <button type="submit" disabled class="btn btn-success" style="display: flex; margin:10px">Submit Attendance  {{ $today->startOfMonth()->toDateString() }} - {{ $today->endOfMonth()->toDateString() }}</button>
                            <div class="d-flex justify-end float-right">
                                <label class="d-flex align-items-center">
                                    <span class="mr-1 font-weight-normal">Data:</span><input type="month" id="month" value="{{ request()->month ?? now()->format('Y-m') }}" class="form-control form-control-sm" placeholder="Selekto muajin" aria-controls="datatable-buttons">
                                </label>
                            </div>
                            @csrf
                            @foreach ($employees as $employee)

                                <input type="hidden" name="emp_id" value="{{ $employee->id }}">

                                <tr>
                                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                    <td>{{ $employee->department->dept_name }}</td>
                                    <!-- <td>{{ $employee->id }}</td> -->
									<!-- Log on to codeastro.com for more projects! -->


                                    @php
                                        $total = 0;
                                        $total_worked = 0;
                                    @endphp



                                    @for ($i = 1; $i < $today->daysInMonth + 1; ++$i)


                                        @php
                                            $date_picker = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
                                            $check_attd = $employee->attendances
                                                ->filter(function ($attendance) use ($date_picker) {
                                                    return date('Y-m-d', strtotime($attendance->punch_time)) === $date_picker;
                                                })
                                                ->first();

                                                $difference = 0;
                                                $hours = 0;
                                                $checkin = $employee->attendances->filter(function ($attendance) use ($date_picker) {
                                                    return date('Y-m-d', strtotime($attendance->punch_time)) === $date_picker;
                                                })->where('punch_state', 0)->sortBy('punch_time')->first();
                                                $break_in = $employee->attendances->filter(function ($attendance) use ($date_picker) {
                                                    return date('Y-m-d', strtotime($attendance->punch_time)) === $date_picker;
                                                })->where('punch_state', 3)->sortBy('punch_time')->first();
                                                $break_out = $employee->attendances->filter(function ($attendance) use ($date_picker) {
                                                    return date('Y-m-d', strtotime($attendance->punch_time)) === $date_picker;
                                                })->where('punch_state', 2)->sortBy('punch_time')->first();
                                                $checkout = $employee->attendances->filter(function ($attendance) use ($date_picker) {
                                                    return date('Y-m-d', strtotime($attendance->punch_time)) === $date_picker;
                                                })->where('punch_state', 1)->sortBy('punch_time')->first();


                                                if($checkin) {
                                                    if($checkout) {
                                                        $difference += \Carbon\Carbon::parse($checkin->punch_time)->diffInSeconds(\Carbon\Carbon::parse($checkout->punch_time));
                                                    } else {
                                                        $difference += \Carbon\Carbon::parse($checkin->punch_time)->diffInSeconds(\Carbon\Carbon::parse($checkin->punch_time));
                                                    }
                                                }

                                                if(is_int($difference) && $difference > 0) {
                                                    $interval = \Carbon\CarbonInterval::seconds($difference);
                                                    $formattedInterval = $interval->cascade()->format('%H:%I:%S');
                                                    // Convert the time string to a Carbon object
                                                    $carbonTime = \Carbon\Carbon::createFromFormat('H:i:s', $formattedInterval === 0 ? '00:00:00' : $formattedInterval);

                                                    // Convert the Carbon object to hours
                                                    $hours = round($carbonTime->hour + ($carbonTime->minute / 60) + ($carbonTime->second / 3600), 1);
                                                    
                                                    $total += $hours;

                                                    $difference = $formattedInterval;
                                                    if($hours > 8) {
                                                        $total_worked += 8;
                                                    } else {
                                                        $total_worked += $hours;
                                                    }
                                                }
                                            
                                            $check_leave = null;
                                            
                                        @endphp
                                        <td>
                                            <span @if($hours == 0) style="color: red;" @endif>{{ $hours }}<span> <br>
                                            <div class="form-check d-none form-check-inline">
                                                
                                                <input class="form-check-input" id="check_box"
                                                    name="attd[{{ $date_picker }}][{{ $employee->id }}]" type="checkbox"
                                                    @if (isset($check_attd))  checked @endif id="inlineCheckbox1" value="1">

                                            </div>

                                        </td>
                                     
                                    @endfor

                                    @php
                                        $hours_total_overtime = $employee->overtimes->sum('total_hr');
                                        $hours_total = $total;
                                        $total_worked = round($total_worked, 2);
                                    @endphp

                                    <td  style="@if($hours_total == 0) color: red; @endif background: #c7fcc7; border-color: #c7fcc7;">
                                        {{ $total_worked }}
                                    </td>
                                    
                                    <td style="background: #c7fcc7; border-color: #c7fcc7;">
                                        {{ $hours_total_overtime }} 
                                        <span style="color: red;" title="Te pa aprovuara">
                                            ({{ round($hours_total - $total_worked - $hours_total_overtime, 2) }})
                                        </span>
                                    </td>

                                    <td  style="@if($hours_total == 0) color: red; @endif background: #c7fcc7; border-color: #c7fcc7;">
                                        {{ $hours_total }}
                                    </td>
                                </tr>
                            @endforeach

                        </form>


                    </tbody>
					<!-- Log on to codeastro.com for more projects! -->


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
