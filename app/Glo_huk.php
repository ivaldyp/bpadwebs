<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_huk extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "glo_huk";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
