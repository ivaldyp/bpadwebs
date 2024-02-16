<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kinerja_foto extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "bpaddtfake.dbo.kinerja_foto";
	// protected $primaryKey = "ids"; 
	// public $incrementing = false;
	public $timestamps = false;
}
