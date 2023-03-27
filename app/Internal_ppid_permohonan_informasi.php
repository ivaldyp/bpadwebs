<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Internal_ppid_permohonan_informasi extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "internal_ppid_permohonan_informasi";
	public $timestamps = false;
}
