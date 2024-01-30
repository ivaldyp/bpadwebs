<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda_tb extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "ids"; 
    protected $table = "bpaddtfake.dbo.agenda_tb";

    public $timestamps = false;
    // public $incrementing = false;
}
