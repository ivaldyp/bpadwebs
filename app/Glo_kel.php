<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_kel extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "bpaddtfake.dbo.glo_kel";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
