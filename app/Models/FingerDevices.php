<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;


class FingerDevices extends Model

{

    use HasFactory;

    protected $table = "iclock_terminal";

    protected $fillable = [

        "name",

        "ip",

        "serialNumber",

    ];

    

}

