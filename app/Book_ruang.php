<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book_ruang extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "ids"; 
    protected $table = "bpaddtfake.dbo.book_ruang";

    public $timestamps = false;
    // public $incrementing = false;
}
