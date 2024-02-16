<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_org_petajab extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "id_emp"; 
    protected $table = "bpaddtfake.dbo.Glo_org_petajab";
    
    public $incrementing = 'false';
    public $timestamps = false;//
}
