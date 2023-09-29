<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'partner_id',
        'event_name',
        'event_description',
        'event_start_date',
        'event_end_date',
        'event_location',
        'is_active',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
