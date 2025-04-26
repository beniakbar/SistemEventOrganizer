<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'event_id', 'status_kehadiran', 'qr_code'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function formJawaban()
    {
        return $this->hasMany(FormJawaban::class);
    }

    public function sertifikat()
    {
        return $this->hasOne(Sertifikat::class);
    }
}