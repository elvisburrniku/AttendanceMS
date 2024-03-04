<?php

namespace App\Helpers;

class ApiUrlHelper
{
    public static function url($name) {
        if($name == 'Employee') {
            return '/personnel/api/employees/?page=1';
        }else if($name == 'Employee.Create') {
            return '/personnel/api/employees/?next=/';
        } else if($name == 'Attendance') {
            return '/iclock/api/transactions/?page=1';
        } else if($name == 'Device') {
            return '/iclock/api/terminals/?page=1';
        } else if($name == 'Department') {
            return 'personnel/api/departments/?page=1';
        }else if($name == 'Position') {
            return 'personnel/api/positions/?page=1';
        }else if($name == 'Area') {
            return 'personnel/api/areas/?page=1';
        }
    }
}