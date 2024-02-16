<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emp_skp extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "bpaddtfake.dbo.emp_skp";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
