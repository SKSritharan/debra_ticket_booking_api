<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_price',
        'allocated_seats',
        'available_quantity',
        'sale_start_date',
        'sale_end_date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketPurchase()
    {
        return $this->hasMany(TicketPurchase::class);
    }
}
