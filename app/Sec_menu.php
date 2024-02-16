<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sec_menu extends Model
{
    protected $connection = 'sqlsrv2';
    protected $primaryKey = "ids"; 
    protected $table = "bpaddtfake.dbo.sec_menu";
    
    public $timestamps = false;

    public function hasMenuToAccess()
    {
        return $this->hasOne('App\Sec_access', 'idtop', 'ids');
    }

    public function parent()
    {
        return $this->belongsTo('App\Sec_menu', 'sao', 'ids');
    }

    public function children()
    {
        return $this->hasMany('App\Sec_menu', 'sao', 'ids');
    }
}
