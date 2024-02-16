<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mob_pushnotif extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "bpaddtfake.dbo.mob_pushnotif";
	// protected $primaryKey = "ids"; 
	// public $incrementing = false;
	public $timestamps = false;
}
