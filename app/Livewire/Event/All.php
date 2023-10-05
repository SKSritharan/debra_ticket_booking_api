<?php

namespace App\Livewire\Event;

use App\Models\Event;
use App\Models\Partner;
use App\Models\Ticket;
use Livewire\Component;
use WireUi\Traits\Actions;

class All extends Component
{
    use Actions;

    public $search = '';
    public $event, $partner_id, $event_id, $ticket, $event_name, $event_description, $event_start_date, $event_end_date, $event_location, $ticket_price, $allocated_seats, $sale_start_date, $sale_end_date;
    public $formModal = false;

    public function boot()
    {
        if (auth()->user()->role->name == 'user'){
            return redirect()->route('tickets');
        }
    }

    public function render()
    {
        $user = auth()->user();
        $events = [];

        if ($user->role->name=='admin') {
            // Admin user can see all events
            $events = Event::search($this->search)->get();
        } else {
            // partner can only see their own events
            $events = $user->partner->events()->search($this->search)->get();
        }

        return view('livewire.event.all', [
            'events' => $events,
            'partners' => Partner::with('user')->get(),
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetCreateForm();
        $this->formModal = true;
    }

    public function resetCreateForm()
    {
        $this->event_name ='';
        $this->event_description = null;
        $this->event_start_date= null;
        $this->event_end_date ='';
        $this->event_location = '';
        $this->ticket_price = 0;
        $this->allocated_seats = 0;
        $this->sale_start_date = null;
        $this->sale_end_date = null;
    }

    public function store()
    {
        $this->validate([
            'event_name' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'event_location' => 'required|string|max:255',
            'ticket_price' => 'required|numeric|min:0',
            'allocated_seats' => 'required|integer|min:1',
            'sale_start_date' => 'required|date',
            'sale_end_date' => 'required|date|after_or_equal:sale_start_date',
        ]);

        if (auth()->user()->role->name == 'partner'){
            $partner = auth()->user()->partner;
        }else{
            $partner = Partner::find($this->partner_id);
        }

        if (!$this->event){
            $event = $partner->events()->create([
                'event_name' => $this->event_name,
                'event_description' => $this->event_description,
                'event_start_date' => $this->event_start_date,
                'event_end_date' => $this->event_end_date,
                'event_location' => $this->event_location,
            ]);
            $ticket = $event->tickets()->create([
                'ticket_price' => $this->ticket_price,
                'allocated_seats' => $this->allocated_seats,
                'sale_start_date' => $this->sale_start_date,
                'sale_end_date' => $this->sale_end_date,
            ]);
        }else{
            $event = $this->event->update([
                'event_name' => $this->event_name,
                'event_description' => $this->event_description,
                'event_start_date' => $this->event_start_date,
                'event_end_date' => $this->event_end_date,
                'event_location' => $this->event_location,
            ]);

            $this->ticket = Ticket::where(['event_id' => $this->event->id])->first();
            $ticket = $this->ticket->update([
                'ticket_price' => $this->ticket_price,
                'allocated_seats' => $this->allocated_seats,
                'sale_start_date' => $this->sale_start_date,
                'sale_end_date' => $this->sale_end_date,
            ]);
        }

        $this->formModal = false;
        $this->notification()->success(
            $title = $this->event_id? 'Event updated': 'Event created',
            $description = $this->event_id? 'The event was successfully updated': 'The event was successfully created'
        );
    }

    public function edit($id)
    {
        $this->formModal = true;
        $this->event = Event::find($id);
        $this->event_id = $id;
        $this->ticket= Ticket::where(['event_id' => $this->event->id])->first();
        $this->partner_id = $this->event->partner_id;
        $this->event_name =$this->event->event_name;
        $this->event_description = $this->event->event_description;
        $this->event_start_date= $this->event->event_start_date;
        $this->event_end_date =$this->event->event_end_date;
        $this->event_location = $this->event->event_location;
        $this->ticket_price = $this->ticket->ticket_price;
        $this->allocated_seats = $this->ticket->allocated_seats;
        $this->sale_start_date = $this->ticket->sale_start_date;
        $this->sale_end_date = $this->ticket->sale_end_date;
    }

    public function deleteConfirmation($id)
    {
        $this->dialog()->confirm([
            'title'       => 'Are you sure you want to delete?',
            'description' => 'This action cannot be undone.',
            'icon'        => 'warning',
            'accept'      => [
                'label'  => 'Yes, delete it',
                'method' => 'delete',
                'params' => 'Deleted',
            ],
            'reject' => [
                'label'  => 'No, cancel',
                'method' => 'cancel',
            ],
        ]);
        $this->event = Event::find($id);
    }

    public function delete()
    {
        if (!$this->event) {
            $this->notification()->error(
                $title = 'Event not found.',
                $description = 'Event deletion failed.'
            );
        }

        // Inactivate the event
        $this->event->update(['is_active' => false]);
        $this->notification()->success(
            $title = 'Event inactivated',
            $description = 'The event was successfully inactivated, cannot be deleted because of ticket purchases.'
        );
    }
}
