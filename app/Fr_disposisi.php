<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fr_disposisi extends Model
{
	protected $connection = 'sqlsrv2';
	// protected $primaryKey = "ids"; 
	protected $table = "bpaddtfake.dbo.fr_disposisi";
	
	// public $incrementing = 'false';
	public $timestamps = false;
}
