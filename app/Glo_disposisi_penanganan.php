<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_disposisi_penanganan extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "bpaddtfake.dbo.glo_disposisi_penanganan";

	public $timestamps = false;
}
