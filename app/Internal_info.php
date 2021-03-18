<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Internal_info extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "ids"; 
    protected $table = "internal_info";

    public $timestamps = false;
    // public $incrementing = false;
}
