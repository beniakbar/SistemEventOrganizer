<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'label', 'field_type', 'is_required'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}