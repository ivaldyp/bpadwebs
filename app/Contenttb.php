<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contenttb extends Model
{
	protected $connection = 'sqlsrv';
    protected $primaryKey = "ids"; 
    protected $table = "content_tb";

    public $timestamps = false;
    // public $incrementing = false;
}
