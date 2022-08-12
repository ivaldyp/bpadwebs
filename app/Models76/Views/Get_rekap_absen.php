<?php

namespace App\Models76\Views;

use Illuminate\Database\Eloquent\Model;

class Get_rekap_absen extends Model
{
    protected $connection = 'server76';
    // protected $primaryKey = "id_emp"; 
    protected $table = "bpaddtfake.dbo.v_get_absen_rekap";
    
    public $incrementing = 'false';
    public $timestamps = false;
}
