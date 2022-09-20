<?php

namespace App\Models76;

use Illuminate\Database\Eloquent\Model;

class Log_bpad extends Model
{
    protected $connection = 'server76';
	protected $table = "bpaddtfake.dbo.SYSLOG_BPADPORTAL";
	public $timestamps = false;
}
