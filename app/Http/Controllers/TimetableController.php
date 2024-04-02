<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use App\Models\Timeinterval;

class TimetableController extends Controller
{
    public function index()
    {
        dd(Timeinterval::with('timetables')->paginate(100));
        // TODO Breaks
        return view('admin.timetable')->with('timetables', Timeinterval::with('timetables')->paginate(100));
    }


    public function store(ScheduleEmp $request)
    {
        $request->validated();

        $schedule = new schedule;
        $schedule->slug = $request->slug;
        $schedule->time_in = $request->time_in;
        $schedule->time_out = $request->time_out;
        $schedule->save();




        flash()->success('Success','Schedule has been created successfully !');
        return redirect()->route('schedule.index');

    }

    public function update(ScheduleEmp $request, Schedule $schedule)
    {
        $request['time_in'] = str_split($request->time_in, 5)[0];
        $request['time_out'] = str_split($request->time_out, 5)[0];

        $request->validated();

        $schedule->slug = $request->slug;
        $schedule->time_in = $request->time_in;
        $schedule->time_out = $request->time_out;
        $schedule->save();
        flash()->success('Success','Schedule has been Updated successfully !');
        return redirect()->route('schedule.index');


    }

  
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        flash()->success('Success','Schedule has been deleted successfully !');
        return redirect()->route('schedule.index');
    }
}