<?php

namespace App\Livewire\Ticket;

use App\Models\TicketPurchase;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.ticket.index', [
            'tickets' => auth()->user()->ticketPurchase()->with('ticket.event')->get(),
        ])->layout('layouts.app');
    }
}
