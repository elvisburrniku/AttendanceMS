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

        $start_t = now()->format('Y-m-d'). ' 00:00';
        $end_t = now()->format('Y-m-d'). ' 23:59';
        $employees = Employee::all();
        $schedules = Schedule::all();
        $shifts = Shift::all();
        $timetables = TimeInterval::all();
        dd($timetables);
        $attendances_for_all = Attendance::whereDate('upload_time', '>=', $start_t)->whereDate('upload_time', '<=', $end_t)->get();
        $attendances = $employees->map(function($emp, $code) use ($schedules, $attendances_for_all, $shifts, $timetables) {
            $attendances_emp = collect($attendances_for_all->where('emp_code', $emp->emp_code)->all());
            $default_emp = (object) [
                'id' => 0,
                'first_name' => $emp->first_name,
                'last_name' => $emp->last_name,
                'upload_time' => now()->format('Y-m-d'),
                'checkin_time' => '',
                'break_in_time' => '',
                'break_out_time' => '',
                'checkout_time' => '',
                'difference' => '',
                'group_ids' => '',
            ];
            $new_employee_for_day = $attendances_emp->first() ?? $default_emp;
            
            $new_employee_for_day->first_name = $emp->first_name;
            $new_employee_for_day->last_name = $emp->last_name;
            $new_employee_for_day->upload_time = $emp->upload_time;

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

        return view('admin.attendance')->with(['attendances' => $attendances]);
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
