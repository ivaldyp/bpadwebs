<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Internal_arsip extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "ids"; 
    protected $table = "bpaddtfake.dbo.internal_arsip";

    public $timestamps = false;
    // public $incrementing = false;
}