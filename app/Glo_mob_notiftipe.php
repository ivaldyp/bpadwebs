<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_mob_notiftipe extends Model
{
    protected $connection = 'sqlsrv2';
	protected $table = "bpaddtfake.dbo.glo_mob_notiftipe";

	public $timestamps = false;
}
