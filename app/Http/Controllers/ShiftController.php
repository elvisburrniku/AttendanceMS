<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\TimeInterval;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with(['timeIntervals', 'schedules'])
                      ->orderBy('alias')
                      ->paginate(20);
        
        return view('admin.shifts.index', compact('shifts'));
    }

    public function create()
    {
        $timeIntervals = TimeInterval::active()->orderBy('alias')->get();
        
        return view('admin.shifts.create', compact('timeIntervals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alias' => 'required|string|max:50|unique:att_attshift,alias',
            'cycle_unit' => 'required|integer|min:1|max:7',
            'shift_cycle' => 'required|integer|min:1',
            'work_weekend' => 'boolean',
            'weekend_type' => 'integer|min:0|max:2',
            'work_day_off' => 'boolean',
            'day_off_type' => 'integer|min:0|max:2',
            'auto_shift' => 'integer|min:0|max:2',
            'enable_ot_rule' => 'boolean',
            'frequency' => 'integer|min:0|max:7',
            'time_intervals' => 'array',
            'time_intervals.*' => 'exists:att_timeinterval,id'
        ]);

        $shift = Shift::create([
            'alias' => $request->alias,
            'cycle_unit' => $request->cycle_unit,
            'shift_cycle' => $request->shift_cycle,
            'work_weekend' => $request->boolean('work_weekend'),
            'weekend_type' => $request->weekend_type ?? 0,
            'work_day_off' => $request->boolean('work_day_off'),
            'day_off_type' => $request->day_off_type ?? 0,
            'auto_shift' => $request->auto_shift ?? 0,
            'enable_ot_rule' => $request->boolean('enable_ot_rule'),
            'frequency' => $request->frequency ?? 1,
            'ot_rule' => $request->ot_rule,
            'company_id' => 1 // Default company ID
        ]);

        // Attach time intervals to shift
        if ($request->has('time_intervals')) {
            foreach ($request->time_intervals as $index => $timeIntervalId) {
                $shift->timeIntervals()->attach($timeIntervalId, [
                    'work_type' => 0,
                    'day_of_week' => $index % 7,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('shifts.index')
                        ->with('success', 'Shift created successfully!');
    }

    public function show(Shift $shift)
    {
        $shift->load(['timeIntervals', 'schedules.employee']);
        
        return view('admin.shifts.show', compact('shift'));
    }

    public function edit(Shift $shift)
    {
        $timeIntervals = TimeInterval::active()->orderBy('alias')->get();
        $shift->load('timeIntervals');
        
        return view('admin.shifts.edit', compact('shift', 'timeIntervals'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'alias' => 'required|string|max:50|unique:att_attshift,alias,' . $shift->id,
            'cycle_unit' => 'required|integer|min:1|max:7',
            'shift_cycle' => 'required|integer|min:1',
            'work_weekend' => 'boolean',
            'weekend_type' => 'integer|min:0|max:2',
            'work_day_off' => 'boolean',
            'day_off_type' => 'integer|min:0|max:2',
            'auto_shift' => 'integer|min:0|max:2',
            'enable_ot_rule' => 'boolean',
            'frequency' => 'integer|min:0|max:7',
            'time_intervals' => 'array',
            'time_intervals.*' => 'exists:att_timeinterval,id'
        ]);

        $shift->update([
            'alias' => $request->alias,
            'cycle_unit' => $request->cycle_unit,
            'shift_cycle' => $request->shift_cycle,
            'work_weekend' => $request->boolean('work_weekend'),
            'weekend_type' => $request->weekend_type ?? 0,
            'work_day_off' => $request->boolean('work_day_off'),
            'day_off_type' => $request->day_off_type ?? 0,
            'auto_shift' => $request->auto_shift ?? 0,
            'enable_ot_rule' => $request->boolean('enable_ot_rule'),
            'frequency' => $request->frequency ?? 1,
            'ot_rule' => $request->ot_rule,
        ]);

        // Sync time intervals
        $shift->timeIntervals()->detach();
        if ($request->has('time_intervals')) {
            foreach ($request->time_intervals as $index => $timeIntervalId) {
                $shift->timeIntervals()->attach($timeIntervalId, [
                    'work_type' => 0,
                    'day_of_week' => $index % 7,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('shifts.index')
                        ->with('success', 'Shift updated successfully!');
    }

    public function destroy(Shift $shift)
    {
        // Check if shift is assigned to any schedules
        if ($shift->schedules()->exists()) {
            return redirect()->route('shifts.index')
                            ->with('error', 'Cannot delete shift that is assigned to schedules. Please remove all schedule assignments first.');
        }

        $shift->timeIntervals()->detach();
        $shift->delete();
        
        return redirect()->route('shifts.index')
                        ->with('success', 'Shift deleted successfully!');
    }

    public function copy(Shift $shift)
    {
        $timeIntervals = TimeInterval::active()->orderBy('alias')->get();
        $shift->load('timeIntervals');
        
        return view('admin.shifts.copy', compact('shift', 'timeIntervals'));
    }

    public function duplicate(Request $request, Shift $shift)
    {
        $request->validate([
            'alias' => 'required|string|max:50|unique:att_attshift,alias',
        ]);

        $newShift = $shift->replicate();
        $newShift->alias = $request->alias;
        $newShift->save();

        // Copy time intervals
        foreach ($shift->timeIntervals as $timeInterval) {
            $newShift->timeIntervals()->attach($timeInterval->id, [
                'work_type' => $timeInterval->pivot->work_type,
                'day_of_week' => $timeInterval->pivot->day_of_week,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('shifts.index')
                        ->with('success', 'Shift duplicated successfully!');
    }
}
