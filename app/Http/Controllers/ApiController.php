<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\TimeInterval;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function employees()
    {
        return response()->json(
            Employee::orderBy('first_name')->get()
        );
    }

    public function shifts()
    {
        return response()->json(
            Shift::with('timeIntervals')->orderBy('alias')->get()
        );
    }

    public function timeIntervals()
    {
        return response()->json(
            TimeInterval::orderBy('alias')->get()
        );
    }

    public function schedules()
    {
        return response()->json(
            Schedule::with(['employee', 'shift.timeIntervals'])
                ->orderBy('start_date', 'desc')
                ->get()
        );
    }

    public function weekData(Request $request)
    {
        $controller = new ShiftCalendarController();
        return $controller->getWeekData($request);
    }

    public function createSchedule(Request $request)
    {
        $controller = new ShiftCalendarController();
        return $controller->createSchedule($request);
    }

    public function updateSchedule(Request $request)
    {
        $controller = new ShiftCalendarController();
        return $controller->updateSchedule($request);
    }

    public function deleteSchedule(Request $request)
    {
        $controller = new ShiftCalendarController();
        return $controller->deleteSchedule($request);
    }
}