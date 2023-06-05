<?php

namespace App\Models11;

use Illuminate\Database\Eloquent\Model;

class Emp_jab extends Model
{
    // protected $connection = 'server11';
    protected $connection = 'server12';
    // protected $primaryKey = "id_emp"; 
    protected $table = "asetmaster.emp.emp_jab";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
