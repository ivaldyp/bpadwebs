<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emp_files extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "emp_files";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
