<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Employee;

class ExportCheckins implements FromView
{
    protected $today;

    public function __construct($today)
    {
        $this->today = $today;
    }

    public function view(): View
    {
        return view('admin.export.check')->with(['today' => $this->today, 'employees' => Employee::with([ 'attendances'=> function($query) {
            $query->whereMonth('punch_time', $this->today);
        }, 'weekdayOvertimes'=> function($query) {
            $query->whereMonth('date', $this->today);
        }, 'weekendOvertimes'=> function($query) {
            $query->whereMonth('date', $this->today);
        }])->get()]);
    }
}
