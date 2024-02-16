<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Disposisi_tes extends Model
{
    protected $connection = 'sqlsrv2';
	// protected $primaryKey = "ids"; 
	protected $table = "bpaddtfake.dbo.disposisi_tes";
	
	// public $incrementing = 'false';
	public $timestamps = false;
}
