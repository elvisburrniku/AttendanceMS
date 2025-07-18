<table>
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
    <thead>
        <tr>
            <th width="200px">Puntori</th>
            <th width="100px">Departmenti</th>
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
            <th style="background: #35dc35; border-color: #35dc35;"> Or vikendi </th>
            <th style="background: #35dc35; border-color: #35dc35;"> Overtime </th>
            <th style="background: #35dc35; border-color: #35dc35;"> Overtime vikendi </th>
            <th style="background: #35dc35; border-color: #35dc35;"> Total </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($employees as $employee)
            <tr>
                <td width="200px">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                <td width="100px">{{ $employee->department->dept_name }}</td>
                <!-- <td>{{ $employee->id }}</td> -->
                @php
                    $total = 0;
                    $total_worked = 0;
                    $total_weekend_hr = 0;
                    $total_overtime_hr = 0;
                    $total_overtime_weekend_hr = 0;
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
                            $carbonTime = null;
                            $hours_rounded = 0;
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
                                $date = \Carbon\Carbon::createFromDate($today->year, $today->month, $i);
                                $interval = \Carbon\CarbonInterval::seconds($difference);
                                $formattedInterval = $interval->cascade()->format('%H:%I:%S');
                                // Convert the time string to a Carbon object
                                $carbonTime = \Carbon\Carbon::createFromFormat('H:i:s', $formattedInterval === 0 ? '00:00:00' : $formattedInterval);
                                
                                // Convert the Carbon object to hours
                                $hours = round($carbonTime->hour + ($carbonTime->minute / 60) + ($carbonTime->second / 3600), 1);
                                $hours_rounded = calculateWorkedHours($hours);
                                $total += $hours_rounded;

                                $difference = $formattedInterval;
                                if($date->isWeekend()) {
                                    if($hours > 8) {
                                        $total_weekend_hr += 8;

                                        $total_overtime_weekend_hr += ($hours_rounded - 8);
                                    } else {
                                        $total_weekend_hr += $hours_rounded;
                                    }
                                } else {
                                    if($hours > 8) {
                                        $total_worked += 8;

                                        $total_overtime_hr += ($hours_rounded - 8);
                                    } else {
                                        $total_worked += $hours_rounded;
                                    }
                                }
                                
                            }
                        
                        $check_leave = null;
                        
                    @endphp
                    <td>
                        <span>{{ $hours_rounded }}<span>
                    </td>
                    
                @endfor

                @php
                    $hours_total_overtime = $employee->weekdayOvertimes->sum('total_hr');
                    $hours_total_overtime_weekend = $employee->weekendOvertimes->sum('total_hr');
                    $hours_total = $total;
                    $total_worked = round($total_worked, 2);
                    $total_weekend_hr = round($total_weekend_hr, 2);
                    $total_overtime_hr = round($total_overtime_hr, 2);
                    $total_overtime_weekend_hr = round($total_overtime_weekend_hr, 2);
                    $total_worked_in_time = convertDecimalToTime($total_worked);
                    $total_weekend_hr_in_time = convertDecimalToTime($total_weekend_hr);
                    $hours_total_overtime_in_time = convertDecimalToTime($total_overtime_hr);
                    $hours_total_overtime_weekend_in_time = convertDecimalToTime($total_overtime_weekend_hr);
                    $hours_total_in_time = convertDecimalToTime($hours_total);
                @endphp

                <td title="{{ $total_worked_in_time }}" style="@if($hours_total == 0) color: red; @endif background: #c7fcc7; border-color: #c7fcc7;cursor:pointer;">
                    {{ $total_worked }}
                </td>

                <td title="{{ $total_weekend_hr_in_time }}" style="@if($hours_total == 0) color: red; @endif background: #c7fcc7; border-color: #c7fcc7;cursor:pointer;">
                    {{ $total_weekend_hr }}
                </td>
                
                <td style="@if($total_overtime_hr == 0) color: red; @endif background: #c7fcc7; border-color: #c7fcc7;">
                    <span style="custor: pointer;" title="{{ $hours_total_overtime_in_time }}">{{ $total_overtime_hr }}<span>
                </td>

                <td style="@if($total_overtime_weekend_hr == 0) color: red; @endif background: #c7fcc7; border-color: #c7fcc7;">
                    <span style="custor: pointer;" title="{{ $hours_total_overtime_weekend_in_time }}">{{ $total_overtime_weekend_hr }}<span>
                </td>

                <td title="{{ $hours_total_in_time }}" style="@if($hours_total == 0) color: red; @endif background: #c7fcc7; border-color: #c7fcc7; cursor: pointer;">
                    {{ $hours_total }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>