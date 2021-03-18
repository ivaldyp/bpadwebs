<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glo_arsip_kategori extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "ids"; 
    protected $table = "glo_arsip_kategori";

    public $timestamps = false;
    // public $incrementing = false;
}
