<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'asc')->get();

        $upcoming_holidays = Holiday::where('date', '>', now())
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.holiday', [
            'holidays' => $holidays,
            'upcoming_holidays' => $upcoming_holidays,
            'year' => now()->year
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function show(Holiday $holiday)
    {
        return view('holidays.show', ['holiday' => $holiday]);
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'comment' => 'nullable',
            'date' => 'required|date',
            'observed_on' => 'required|date',
        ]);

        $holiday = Holiday::create([
            'type' => $request->input('type'),
            'comment' => $request->input('comment') ?? '',
            'date' => $request->input('date'),
            'observedOn' => $request->input('observed_on'),
        ]);

        if ($request->wantsJson()) {
            return $holiday;
        }

        return redirect()->route('holiday');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday)
    {
        return view('admin.holidays.edit', ['holiday' => $holiday]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'type' => 'required',
            'comment' => 'required',
            'date' => 'required|date',
            'observed_on' => 'required|date',
        ]);

        $holiday->update([
            'type' => $request->input('type'),
            'comment' => $request->input('comment'),
            'date' => $request->input('date'),
            'observedOn' => $request->input('observed_on'),
        ]);

        if ($request->wantsJson()) {
            return $holiday;
        }

        return redirect()->route('holiday');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Holiday $holiday
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        if (request()->wantsJson()) {
            return response(['message' => 'Holiday was successfully deleted.'], Response::HTTP_NO_CONTENT);
        }

        return redirect()->route('holiday');
    }
}
