<?php

namespace App\Livewire\Event;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Livewire\Component;
use WireUi\Traits\Actions;

class Index extends Component
{
    use Actions;

    public $search = '';
    public $user;
    public $event_id;

    public function render()
    {
        return view('livewire.event.index',[
            'events' => Event::search($this->search)->get(),
        ])->layout('layouts.base');
    }

    public function purchaseRequest($id)
    {
        $this->dialog()->confirm([
            'title' => 'Are you sure?',
            'description' => 'Do you want to purchase this ticket?',
            'icon' => 'question',
            'accept' => [
                'label' => 'Yes, purchase',
                'method' => 'purchase',
                'params' => $id,
            ],
            'reject' => [
                'label' => 'No, cancel',
                'method' => 'cancel',
            ],
        ]);
    }

    public function purchase($id)
    {
        // Check if the user is authenticated (logged in)
        if (!auth()->check()) {
            $this->notification()->error('Not logged in', 'You must be logged in to purchase a ticket.');
            return;
        }

        $ticket = Ticket::where('event_id', $id)->first();

        if (!$ticket) {
            $this->notification()->error('Ticket not found', 'The ticket you requested is not available.');
            return;
        }

        if ($ticket->available_quantity <= 0) {
            $this->notification()->error('Ticket is sold out', 'This ticket is sold out.');
            return;
        }

        $ticket->decrement('available_quantity');

        TicketPurchase::create([
            'user_id' => auth()->user()->id,
            'ticket_id' => $ticket->id,
            'purchase_date' => now(),
        ]);

        $this->notification()->success('Ticket purchased', 'You have successfully purchased a ticket.');
    }
}
