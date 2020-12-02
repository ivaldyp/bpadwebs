<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content_tb extends Model
{
	protected $connection = 'sqlsrv2';
    // protected $primaryKey = "ids"; 
    protected $table = "fr_disposisi";

    public $timestamps = false;
    // public $incrementing = false;
}
