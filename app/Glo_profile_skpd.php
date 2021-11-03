<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_profile_skpd extends Model
{
    protected $connection = 'sqlsrv2';
	// protected $primaryKey = "ids"; 
	protected $table = "glo_profile_skpd";
	
	// public $incrementing = 'false';
	public $timestamps = false;
}
