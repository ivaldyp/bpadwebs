<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emp_non extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "emp_non";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
