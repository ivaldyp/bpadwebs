<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dta_kaban_event extends Model
{
    protected $connection = 'server11';
	// protected $primaryKey = "ids"; 
	protected $table = "bpadmobile.dbo.dta_kaban_event";
	
	// public $incrementing = 'false';
	public $timestamps = false;
}
