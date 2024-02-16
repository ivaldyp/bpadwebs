<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_tujuan_kehadiran extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "bpaddtfake.dbo.glo_tujuan_kehadiran";
	// protected $primaryKey = "ids"; 
	// public $incrementing = false;
	public $timestamps = false;
}
