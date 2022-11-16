<?php

namespace App\Models76;

use Illuminate\Database\Eloquent\Model;

class Log_bpad extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "bpaddtfake.dbo.SYSLOG_BPADPORTAL";
    public $incrementing = 'false';
    public $timestamps = false;
}
