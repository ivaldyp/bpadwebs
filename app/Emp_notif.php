<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emp_notif extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "bpaddtfake.dbo.emp_notif";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
