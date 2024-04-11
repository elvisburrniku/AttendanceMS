<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\TimeInterval;
use App\Models\Latetime;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AttendanceEmp;
use App\Helpers\ApiHelper;
use App\Helpers\ApiUrlHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\CarbonInterval;

class AttendanceController extends Controller
{   
    //show attendance 
    public function index()
    {  

        $start_t = (request()->date ?? now()->format('Y-m-d')). ' 00:00';
        $end_t = (request()->date ?? now()->format('Y-m-d')). ' 23:59';

        $next = \Carbon\Carbon::parse($start_t)->addDay();
        $prev = \Carbon\Carbon::parse($start_t)->subDay();
        $employees = Employee::all();
        $schedules = Schedule::all();
        $shifts = Shift::all();
        $timetables = TimeInterval::all();
        $attendances_for_all = Attendance::whereDate('punch_time', $start_t)->whereDate('punch_time', '<=', $end_t)->get();
        $attendances = $employees->map(function($emp, $code) use ($schedules, $attendances_for_all, $shifts, $timetables) {
            $attendances_emp = collect($attendances_for_all->where('emp_code', $emp->emp_code)->all());
            $default_emp = (object) [
                'id' => $emp->id,
                'user_id' => $emp->id,
                'first_name' => $emp->first_name,
                'last_name' => $emp->last_name,
                'upload_time' => request()->date ?? now()->format('Y-m-d'),
                'checkin_time' => '',
                'break_in_time' => '',
                'break_out_time' => '',
                'checkout_time' => '',
                'difference' => '',
                'group_ids' => '',
            ];
            $new_employee_for_day = $attendances_emp->first() ?? $default_emp;
            $new_employee_for_day->user_id = $emp->id;
            $new_employee_for_day->first_name = $emp->first_name;
            $new_employee_for_day->last_name = $emp->last_name;
            $new_employee_for_day->upload_time = $default_emp->upload_time;

            // Timetable
            $shift = null;
            $schedule = null;

            $schedule = (object) $schedules->where('employee', $emp->id)->first();
            $shift = (object) $shifts->where('id', optional($schedule)->shift)->first();
            $shift_name = optional($shift)->shift_timetable;
            $timetable = (object) $timetables->where('alias', $shift_name)->first();

            $group_ids = $attendances_emp->pluck('id');
            $checkin = $attendances_emp->where('punch_state', 0)->sortBy('punch_time')->first();
            $new_employee_for_day->checkin_time = $this->convertToTime(optional($checkin)->punch_time);
            $new_employee_for_day->checkin_time_date = $new_employee_for_day->checkin_time;

            if(optional($timetable)->in_time && $checkin){
                $timetableInDateTime = DateTime::createFromFormat('H:i:s', optional($timetable)->in_time);
                $newEmployeeCheckinDateTime = DateTime::createFromFormat('H:i', $new_employee_for_day->checkin_time);
            
                if($timetableInDateTime < $newEmployeeCheckinDateTime) {
                    $new_employee_for_day->checkin_time = "<p class='text-danger'>$new_employee_for_day->checkin_time</p>";
                    $new_employee_for_day->is_checkin_late = true;
                } else if($timetableInDateTime > $newEmployeeCheckinDateTime) {
                    $new_employee_for_day->checkin_time = "<p class='text-danger'>$new_employee_for_day->checkin_time</p>";
                    $new_employee_for_day->is_checkin_late = false;
                }
            } else if(!$checkin) {
                $new_employee_for_day->checkin_time = '<p class="text-danger">MUNGON</p>';
            }
            $break_in = $attendances_emp->where('punch_state', 3)->sortBy('punch_time')->first();
            $new_employee_for_day->break_in_time = $this->convertToTime(optional($break_in)->punch_time);

            $break_out = $attendances_emp->where('punch_state', 2)->sortBy('punch_time')->first();
            $new_employee_for_day->break_out_time = $this->convertToTime(optional($break_out)->punch_time);


            $checkout = $attendances_emp->where('punch_state', 1)->sortBy('punch_time')->first();
            $new_employee_for_day->checkout_time = $this->convertToTime(optional($checkout)->punch_time);

            if(optional($timetable)->out_time && $checkout){
                $timetableInDateTime = DateTime::createFromFormat('H:i:s', optional($timetable)->out_time);
                $newEmployeeCheckoutDateTime = DateTime::createFromFormat('H:i:s', $new_employee_for_day->checkout_time);
              
                if($timetableInDateTime < $newEmployeeCheckoutDateTime) {
                    $new_employee_for_day->checkout_time = "<p class='text-success'>$new_employee_for_day->checkout_time<p>";
                    $new_employee_for_day->is_checkin_late = true;
                } else if($timetableInDateTime > $newEmployeeCheckoutDateTime) {
                    $new_employee_for_day->checkout_time = "<p class='text-danger'>$new_employee_for_day->checkout_time</p>";
                    $new_employee_for_day->is_checkin_late = false;
                }
            }
            $difference = 0;

            if($checkin) {
                if($checkout) {
                    $difference = \Carbon\Carbon::parse($checkin->punch_time)->diffInSeconds(\Carbon\Carbon::parse($checkout->punch_time));
                } else {
                    $difference = \Carbon\Carbon::parse($checkin->punch_time)->diffInSeconds(\Carbon\Carbon::parse(now()));
                }
            }

            if($checkin && $break_in) {
                if($break_out) {
                    $difference_pause = \Carbon\Carbon::parse($break_in->punch_time)->diffInSeconds(\Carbon\Carbon::parse($break_out->punch_time));
                } else {
                    $difference = \Carbon\Carbon::parse($checkin->punch_time)->diffInSeconds(\Carbon\Carbon::parse($break_in->punch_time));
                }
            }

            if($difference > 0) {
                $interval = CarbonInterval::seconds($difference);
                $formattedInterval = $interval->cascade()->format('%H:%I:%S');

                $difference = $formattedInterval;
            }

            $new_employee_for_day->difference = $difference;

            $new_employee_for_day->group_ids = $group_ids;

            return $new_employee_for_day;
        });

        return view('admin.attendance')->with(['attendances' => $attendances, 'next' => $next, 'prev' => $prev]);
    }

    protected function convertToTime($date_time)
    {
        if($date_time) {
            return \Carbon\Carbon::parse($date_time)->format('H:i');
        }

        return null;
    }

    //show late times
    public function indexLatetime()
    {
        return view('admin.latetime')->with(['latetimes' => Latetime::all()]);
    }

    

    // public static function lateTime(Employee $employee)
    // {
    //     $current_t = new DateTime(date('H:i:s'));
    //     $start_t = new DateTime($employee->schedules->first()->time_in);
    //     $difference = $start_t->diff($current_t)->format('%H:%I:%S');

    //     $latetime = new Latetime();
    //     $latetime->emp_id = $employee->id;
    //     $latetime->duration = $difference;
    //     $latetime->latetime_date = date('Y-m-d');
    //     $latetime->save();
    // }

    public static function lateTimeDevice($att_dateTime, Employee $employee)
    {
        $attendance_time = new DateTime($att_dateTime);
        $checkin = new DateTime($employee->schedules->first()->time_in);
        $difference = $checkin->diff($attendance_time)->format('%H:%I:%S');

        $latetime = new Latetime();
        $latetime->emp_id = $employee->id;
        $latetime->duration = $difference;
        $latetime->latetime_date = date('Y-m-d', strtotime($att_dateTime));
        $latetime->save();
    }

    public function update(Request $request, $employee_id)
    {
        $employee = Employee::find($employee_id);
        $start_t = (request()->date ?? now()->format('Y-m-d')). ' 00:00';
        $end_t = (request()->date ?? now()->format('Y-m-d')). ' 23:59';
        $attendances = Attendance::whereDate('upload_time', '>=', $start_t)->whereDate('upload_time', '<=', $end_t)->where('emp_code', $employee->emp_code)->get();
        $checkin = $attendances->where('punch_state', '0')->sortBy('punch_time')->first();
        $checkout = $attendances->where('punch_state', '1')->sortBy('punch_time')->first();
        $break_in = $attendances->where('punch_state', '3')->sortBy('punch_time')->first();
        $break_out = $attendances->where('punch_state', '2')->sortBy('punch_time')->first();
        $random_attendance = Attendance::first();

        if($checkin && $request->checkin_time) {
            $checkin->punch_time = \Carbon\Carbon::parse($request->date. ' '. $request->checkin_time);
            $checkin->save();
        } else if(($checkin = Attendance::whereDate('punch_time', $request->date)->where('punch_state', "0")->where('emp_code', $employee->emp_code)->first()) && $request->checkin_time) {
            $checkin->punch_time = \Carbon\Carbon::parse($request->date. ' '. $request->checkin_time);
            $checkin->save();
        } else if ($request->checkin_time) {
            $checkin = Attendance::insert([
                "emp_code" => $employee->emp_code,
                "punch_time" => \Carbon\Carbon::parse($request->date. ' '.$request->checkin_time),
                "punch_state" => "0",
                "verify_type" => $random_attendance->verify_type,
                "work_code" => $random_attendance->work_code,
                "terminal_sn" => $random_attendance->terminal_sn,
                "terminal_alias" => $random_attendance->terminal_alias,
                "area_alias" => $random_attendance->area_alias,
                "longitude" => $random_attendance->longitude,
                "latitude" => $random_attendance->latitude,
                "gps_location" => $random_attendance->gps_location,
                "mobile" => $random_attendance->mobile,
                "source" => $random_attendance->source,
                "purpose" => $random_attendance->purpose,
                "crc" => $random_attendance->crc,
                "is_attendance" => $random_attendance->is_attendance,
                "reserved" => $random_attendance->reserved,
                "upload_time" => $random_attendance->upload_time,
                "sync_status" => $random_attendance->sync_status,
                "sync_time" => $random_attendance->sync_time,
                "is_mask" => $random_attendance->is_mask,
                "temperature" => $random_attendance->temperature,
                "emp_id" => $employee->id,
                "terminal_id" => $random_attendance->terminal_id,
                "company_code" => $random_attendance->company_code,
            ]);
        }

        if($checkout && $request->checkout_time) {
            $checkout->punch_time = \Carbon\Carbon::parse($request->date. ' '. $request->checkout_time);
            $checkout->save();
        } else if(($checkout = Attendance::whereDate('punch_time', $request->date)->where('punch_state', "1")->where('emp_code', $employee->emp_code)->first()) && $request->checkout_time) {
            $checkout->punch_time = \Carbon\Carbon::parse($request->date. ' '. $request->checkout_time);
            $checkout->save();
        } else if ($request->checkout_time) {
            $checkout = Attendance::insert([
                "emp_code" => $employee->emp_code,
                "punch_time" => \Carbon\Carbon::parse($request->date. ' '.$request->checkout_time),
                "punch_state" => "1",
                "verify_type" => $random_attendance->verify_type,
                "work_code" => $random_attendance->work_code,
                "terminal_sn" => $random_attendance->terminal_sn,
                "terminal_alias" => $random_attendance->terminal_alias,
                "area_alias" => $random_attendance->area_alias,
                "longitude" => $random_attendance->longitude,
                "latitude" => $random_attendance->latitude,
                "gps_location" => $random_attendance->gps_location,
                "mobile" => $random_attendance->mobile,
                "source" => $random_attendance->source,
                "purpose" => $random_attendance->purpose,
                "crc" => $random_attendance->crc,
                "is_attendance" => $random_attendance->is_attendance,
                "reserved" => $random_attendance->reserved,
                "upload_time" => $random_attendance->upload_time,
                "sync_status" => $random_attendance->sync_status,
                "sync_time" => $random_attendance->sync_time,
                "is_mask" => $random_attendance->is_mask,
                "temperature" => $random_attendance->temperature,
                "emp_id" => $employee->id,
                "terminal_id" => $random_attendance->terminal_id,
                "company_code" => $random_attendance->company_code,
            ]);
        }

        if($break_in && $request->break_in_time) {
            $break_in->punch_time = \Carbon\Carbon::parse($request->date. ' '. $request->break_in_time);
            $break_in->save();
        } else if(($break_in = Attendance::whereDate('punch_time', $request->date)->where('punch_state', "3")->where('emp_code', $employee->emp_code)->first()) && $request->break_in_time) {
            $break_in->punch_time = \Carbon\Carbon::parse($request->date. ' '. $request->break_in_time);
            $break_in->save();
        } else if ($request->break_in_time) {
            $break_in = Attendance::insert([
                "emp_code" => $employee->emp_code,
                "punch_time" => \Carbon\Carbon::parse($request->date. ' '.$request->break_in_time),
                "punch_state" => "3",
                "verify_type" => $random_attendance->verify_type,
                "work_code" => $random_attendance->work_code,
                "terminal_sn" => $random_attendance->terminal_sn,
                "terminal_alias" => $random_attendance->terminal_alias,
                "area_alias" => $random_attendance->area_alias,
                "longitude" => $random_attendance->longitude,
                "latitude" => $random_attendance->latitude,
                "gps_location" => $random_attendance->gps_location,
                "mobile" => $random_attendance->mobile,
                "source" => $random_attendance->source,
                "purpose" => $random_attendance->purpose,
                "crc" => $random_attendance->crc,
                "is_attendance" => $random_attendance->is_attendance,
                "reserved" => $random_attendance->reserved,
                "upload_time" => $random_attendance->upload_time,
                "sync_status" => $random_attendance->sync_status,
                "sync_time" => $random_attendance->sync_time,
                "is_mask" => $random_attendance->is_mask,
                "temperature" => $random_attendance->temperature,
                "emp_id" => $employee->id,
                "terminal_id" => $random_attendance->terminal_id,
                "company_code" => $random_attendance->company_code,
            ]);
        }


        if($break_out && $request->break_out_time) {
            $break_out->punch_time = \Carbon\Carbon::parse($request->date. ' '. $request->break_out_time);
            $break_out->save();
        } else if(($break_out = Attendance::whereDate('punch_time', $request->date)->where('punch_state', "2")->where('emp_code', $employee->emp_code)->first()) && $request->break_out_time) {
            $break_out->punch_time = \Carbon\Carbon::parse($request->date. ' '. $request->break_out_time);
            $break_out->save();
        } else if ($request->break_out_time) {
            $break_out = Attendance::insert([
                "emp_code" => $employee->emp_code,
                "punch_time" => \Carbon\Carbon::parse($request->date. ' '.$request->break_out_time),
                "punch_state" => "2",
                "verify_type" => $random_attendance->verify_type,
                "work_code" => $random_attendance->work_code,
                "terminal_sn" => $random_attendance->terminal_sn,
                "terminal_alias" => $random_attendance->terminal_alias,
                "area_alias" => $random_attendance->area_alias,
                "longitude" => $random_attendance->longitude,
                "latitude" => $random_attendance->latitude,
                "gps_location" => $random_attendance->gps_location,
                "mobile" => $random_attendance->mobile,
                "source" => $random_attendance->source,
                "purpose" => $random_attendance->purpose,
                "crc" => $random_attendance->crc,
                "is_attendance" => $random_attendance->is_attendance,
                "reserved" => $random_attendance->reserved,
                "upload_time" => $random_attendance->upload_time,
                "sync_status" => $random_attendance->sync_status,
                "sync_time" => $random_attendance->sync_time,
                "is_mask" => $random_attendance->is_mask,
                "temperature" => $random_attendance->temperature,
                "emp_id" => $employee->id,
                "terminal_id" => $random_attendance->terminal_id,
                "company_code" => $random_attendance->company_code,
            ]);
        }

        return redirect()->back();
    }
  
    public function destroy(Request $request, $main_id)
    {
        $group_ids = json_decode($request->attendance_ids);

        foreach($group_ids as $id) {
            $api = new ApiHelper();

            $api->url(ApiUrlHelper::url('Attendance.Delete'));

            $api->delete($id);
        }

        flash()->success('Success','Attendance Record has been Deleted successfully !');
        return redirect()->route('attendance')->with('success');
    }

    public function export()
    {
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Attendance.Export'));

        $export = $api->get();
        $ip = "46.99.253.82:8089";
        $task_id = $export->get('payload')['task_id'];

        // $api->url('/files/reports/20240309/'.$export->get('payload')['task_id'].'.xlsx');
        $response = Http::get("http://$ip/files/reports/20240309/$task_id.xlsx");

        sleep(10);
        
        // Check if the request was successful
        if ($response->successful()) {
            // Set headers for downloadable file
            $headers = [
                'Content-Type' => $response->header('Content-Type'),
                'Content-Disposition' => 'attachment; filename="Transaction_20240309123720_export.xlsx"',
            ];
            
            // Return downloadable file response
            return response($response->body(), 200, $headers);
        } else {
            // Handle the error if the request was not successful
            $statusCode = $response->status();
            $errorMessage = $response->body();
            // Handle the error according to your application's logic
            // For example, log the error or return an error response
            return response()->json(['error' => $errorMessage], $statusCode);
        }
    }
}
