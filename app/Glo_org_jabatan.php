<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_org_jabatan extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "bpaddtfake.dbo.glo_org_jabatan";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
