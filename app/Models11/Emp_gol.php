<?php

namespace App\Models11;

use Illuminate\Database\Eloquent\Model;

class Emp_gol extends Model
{
    protected $connection = 'server11';
    // protected $primaryKey = "id_emp"; 
    protected $table = "asetmaster.emp.emp_gol";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
