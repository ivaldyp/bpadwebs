<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setup_can_approve extends Model
{
    protected $connection = 'sqlsrv';
    // protected $primaryKey = "ids"; 
    protected $table = "setup_can_approve";
}
