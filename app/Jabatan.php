<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected  $fillable = ['id','nama_jabatan','eselon','created_at','updated_at'];
}
