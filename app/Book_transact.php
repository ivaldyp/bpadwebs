<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book_transact extends Model
{
    protected $connection = 'sqlsrv2';
    // protected $primaryKey = "ids"; 
    protected $table = "book_transact";

    public $timestamps = false;
    // public $incrementing = false;
}
