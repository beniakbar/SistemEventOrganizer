<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_event', 'deskripsi', 'jadwal', 'lokasi', 'kuota', 'jenis', 'is_sertifikat', 'is_active'
    ];

    public function formFields()
    {
        return $this->hasMany(EventFormField::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }
}