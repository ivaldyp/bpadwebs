<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_org_golongan extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "bpaddtfake.dbo.glo_org_golongan";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
