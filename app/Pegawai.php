<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $fillable = ['nip','nip18','nama_lengkap','no_hp','email','foto','unit_id','unit_jabatan_id','jabatan_id','user_id','nip_atas','status','role','created_at','updated_at'];

    public function getFoto()
    {
        if (!$this->foto){
            return asset('images/user.png');
        }
        return asset('images/'.$this->foto);
    }
}
