<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pem_carousel extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "pem_carousel";
	// protected $primaryKey = "ids"; 
	// public $incrementing = false;
	public $timestamps = false;
}
