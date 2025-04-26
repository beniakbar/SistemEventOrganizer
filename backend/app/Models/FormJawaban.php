<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormJawaban extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_id', 'field_id', 'jawaban'
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function field()
    {
        return $this->belongsTo(EventFormField::class, 'field_id');
    }
}