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

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function scopeSearch($query, $searchTerm)
    {
        if ($searchTerm) {
            return $query->where('event_name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('event_description', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('event_location', 'LIKE', '%' . $searchTerm . '%');
        }

        return $query;
    }
}
