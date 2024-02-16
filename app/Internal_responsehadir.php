<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Internal_responsehadir extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "bpaddtfake.dbo.internal_responsehadir";
	// protected $primaryKey = "ids"; 
	// public $incrementing = false;
	public $timestamps = false;
}
