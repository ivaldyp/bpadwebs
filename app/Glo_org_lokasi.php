<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_org_lokasi extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "bpaddtfake.dbo.glo_org_lokasi";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
