<?php

namespace App\Helpers;

class ApiUrlHelper
{
    public static function url($name) {
        if($name == 'Employee') {
            return '/personnel/api/employees/?page=1';
        }else if($name == 'Employee.Create') {
            return '/personnel/api/employees/?next=/';
        }else if($name == 'Employee.Update') {
            return '/personnel/api/employees';
        } else if($name == 'Attendance') {
            return '/iclock/api/transactions/?page=1&page_size=50';
        } else if($name == 'Attendance.Delete') {
            return '/iclock/api/transactions';
        }else if($name == 'Attendance.Export') {
            return '/iclock/api/transactions/export/?export_type=xls';
        } else if($name == 'Device') {
            return '/iclock/api/terminals/?page=1';
        } else if($name == 'Department') {
            return 'personnel/api/departments/?page=1&page_size=13';
        } else if($name == 'Department.Update') {
            return 'personnel/api/departments';
        } else if($name == 'Position') {
            return 'personnel/api/positions/?page=1&page_size=13';
        }else if($name == 'Position.Update') {
            return 'personnel/api/positions';
        }else if($name == 'Area') {
            return 'personnel/api/areas/?page=1';
        }else if($name == 'Area.Update') {
            return 'personnel/api/areas';
        } else if($name == 'Transaction.Report') {
            return 'att/api/transactionReport/?page=1&page_size=50';
        }else if($name == 'Shift') {
            return 'att/api/attshifts/?page=1&page_size=200';
        }else if($name == 'Schedules') {
            return 'att/api/attschedules/?page=1&page_size=200';
        }else if($name == 'BreakTime') {
            return 'att/api/breaktimes/?page=1&page_size=200';
        }else if($name == 'TimeInterval') {
            return 'att/api/timeintervals/?page=1&page_size=200';
        }else if($name == 'Attendances.Api') {
            return 'att/api/?page=1&page_size=50';
        }
    }
}