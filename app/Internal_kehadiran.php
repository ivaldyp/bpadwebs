<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Internal_kehadiran extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "bpaddtfake.dbo.internal_kehadiran";
	// protected $primaryKey = "ids"; 
	// public $incrementing = false;
	public $timestamps = false;
}
