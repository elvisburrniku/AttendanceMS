<?php

namespace App\Helpers;

class ApiUrlHelper
{
    public static function url($name) {
        if($name == 'Employee') {
            return '/personnel/api/employees/?page=1';
        } else if($name == 'Attendance') {
            return '/iclock/api/transactions/?page=1';
        }
    }
}