<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notulen extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "bpaddtfake.dbo.notulen";
	// protected $primaryKey = "ids"; 
	// public $incrementing = false;
	public $timestamps = false;
}
