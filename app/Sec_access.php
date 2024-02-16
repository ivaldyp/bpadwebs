<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sec_access extends Model
{
    protected $connection = 'sqlsrv2';
    protected $primaryKey = null; 
    protected $table = "bpaddtfake.dbo.sec_access";
    public $timestamps = false;
    
    public function belongAccessToMenu()
    {
        return $this->hasMany('App\Sec_menu', 'ids', 'idtop');
    }
}
