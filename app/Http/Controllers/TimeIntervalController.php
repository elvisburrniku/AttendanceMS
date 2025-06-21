<?php

namespace App\Http\Controllers;

use App\Models\TimeInterval;
use Illuminate\Http\Request;

class TimeIntervalController extends Controller
{
    public function index()
    {
        $timeIntervals = TimeInterval::orderBy('alias')->paginate(20);
        
        return view('admin.time-intervals.index', compact('timeIntervals'));
    }

    public function create()
    {
        return view('admin.time-intervals.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'alias' => 'required|string|max:50|unique:att_timeinterval,alias',
            'in_time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:1',
            'in_ahead_margin' => 'integer|min:0',
            'in_above_margin' => 'integer|min:0',
            'out_ahead_margin' => 'integer|min:0',
            'out_above_margin' => 'integer|min:0',
            'allow_late' => 'integer|min:0',
            'allow_leave_early' => 'integer|min:0',
            'work_day' => 'numeric|min:0|max:1',
        ]);

        TimeInterval::create([
            'alias' => $request->alias,
            'use_mode' => 1, // Active by default
            'in_time' => $request->in_time,
            'duration' => $request->duration,
            'in_ahead_margin' => $request->in_ahead_margin ?? 0,
            'in_above_margin' => $request->in_above_margin ?? 0,
            'out_ahead_margin' => $request->out_ahead_margin ?? 0,
            'out_above_margin' => $request->out_above_margin ?? 0,
            'in_required' => 1,
            'out_required' => 1,
            'allow_late' => $request->allow_late ?? 0,
            'allow_leave_early' => $request->allow_leave_early ?? 0,
            'work_day' => $request->work_day ?? 1,
            'early_in' => 0,
            'min_early_in' => 0,
            'late_out' => 0,
            'min_late_out' => 0,
            'overtime_lv' => 0,
            'overtime_lv1' => 0,
            'overtime_lv2' => 0,
            'overtime_lv3' => 0,
            'multiple_punch' => 0,
            'available_interval_type' => 0,
            'available_interval' => 0,
            'work_time_duration' => $request->duration,
            'func_key' => 0,
            'work_type' => 0,
            'day_change' => '00:00',
            'enable_early_in' => $request->boolean('enable_early_in'),
            'enable_late_out' => $request->boolean('enable_late_out'),
            'enable_overtime' => $request->boolean('enable_overtime'),
            'color_setting' => '#3498db',
            'enable_max_ot_limit' => $request->boolean('enable_max_ot_limit'),
            'max_ot_limit' => $request->max_ot_limit ?? 0,
            'count_early_in_interval' => $request->boolean('count_early_in_interval'),
            'count_late_out_interval' => $request->boolean('count_late_out_interval'),
            'overtime_policy' => 0,
            'company_id' => 1
        ]);

        return redirect()->route('time-intervals.index')
                        ->with('success', 'Time interval created successfully!');
    }

    public function show(TimeInterval $timeInterval)
    {
        $timeInterval->load('shifts');
        
        return view('admin.time-intervals.show', compact('timeInterval'));
    }

    public function edit(TimeInterval $timeInterval)
    {
        return view('admin.time-intervals.edit', compact('timeInterval'));
    }

    public function update(Request $request, TimeInterval $timeInterval)
    {
        $request->validate([
            'alias' => 'required|string|max:50|unique:att_timeinterval,alias,' . $timeInterval->id,
            'in_time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:1',
            'in_ahead_margin' => 'integer|min:0',
            'in_above_margin' => 'integer|min:0',
            'out_ahead_margin' => 'integer|min:0',
            'out_above_margin' => 'integer|min:0',
            'allow_late' => 'integer|min:0',
            'allow_leave_early' => 'integer|min:0',
            'work_day' => 'numeric|min:0|max:1',
        ]);

        $timeInterval->update([
            'alias' => $request->alias,
            'in_time' => $request->in_time,
            'duration' => $request->duration,
            'in_ahead_margin' => $request->in_ahead_margin ?? 0,
            'in_above_margin' => $request->in_above_margin ?? 0,
            'out_ahead_margin' => $request->out_ahead_margin ?? 0,
            'out_above_margin' => $request->out_above_margin ?? 0,
            'allow_late' => $request->allow_late ?? 0,
            'allow_leave_early' => $request->allow_leave_early ?? 0,
            'work_day' => $request->work_day ?? 1,
            'work_time_duration' => $request->duration,
            'enable_early_in' => $request->boolean('enable_early_in'),
            'enable_late_out' => $request->boolean('enable_late_out'),
            'enable_overtime' => $request->boolean('enable_overtime'),
            'enable_max_ot_limit' => $request->boolean('enable_max_ot_limit'),
            'max_ot_limit' => $request->max_ot_limit ?? 0,
            'count_early_in_interval' => $request->boolean('count_early_in_interval'),
            'count_late_out_interval' => $request->boolean('count_late_out_interval'),
        ]);

        return redirect()->route('time-intervals.index')
                        ->with('success', 'Time interval updated successfully!');
    }

    public function destroy(TimeInterval $timeInterval)
    {
        // Check if time interval is used in any shifts
        if ($timeInterval->shifts()->exists()) {
            return redirect()->route('time-intervals.index')
                            ->with('error', 'Cannot delete time interval that is used in shifts. Please remove from shifts first.');
        }

        $timeInterval->delete();
        
        return redirect()->route('time-intervals.index')
                        ->with('success', 'Time interval deleted successfully!');
    }

    public function toggle(TimeInterval $timeInterval)
    {
        $timeInterval->update([
            'use_mode' => $timeInterval->use_mode > 0 ? 0 : 1
        ]);

        $status = $timeInterval->use_mode > 0 ? 'activated' : 'deactivated';
        
        return redirect()->route('time-intervals.index')
                        ->with('success', "Time interval {$status} successfully!");
    }
}