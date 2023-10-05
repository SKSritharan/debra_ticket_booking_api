<?php

namespace App\Livewire\Earning;

use App\Models\Event;
use App\Models\Partner;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $partnersInfo;

    public function boot()
    {
        if (auth()->user()->role->name == 'user'){
            return redirect()->route('tickets');
        }
    }

    public function mount()
    {
        $user = auth()->user()->role->name;

        if ($user == 'partner'){
            $partners = [auth()->user()->partner];
        }else{
            $partners = Partner::all();
        }

        $partnersInfo = [];

        foreach ($partners as $partner) {
            $partnerId = $partner->id;

            $partnerInfo = [
                'id' => $partner->id,
                'name'=> $partner->user->name,
                'contact_number' => $partner->contact_number,
                'company_name' => $partner->company_name,
                'status' => $partner->status,
            ];

            $events = Event::where('partner_id', $partnerId)->get();

            $earningsByEvent = [];
            $totalEarnings = 0;

            foreach ($events as $event) {
                $earnings = DB::table('tickets')
                    ->where('event_id', $event->id)
                    ->sum(DB::raw('ticket_price * (allocated_seats - available_quantity)'));

                $eventDetails = [
                    'id' => $event->id,
                    'name' => $event->event_name,
                    'description' => $event->event_description,
                    'start_date' => $event->event_start_date,
                    'end_date' => $event->event_end_date,
                    'location' => $event->event_location,
                ];

                $earningsByEvent[] = [
                    'event' => $eventDetails,
                    'earning' => 'Rs.'.$earnings,
                ];

                $totalEarnings += $earnings;
            }

            $partnersInfo[] = [
                'partner_info' => $partnerInfo,
                'events' => $earningsByEvent,
                'total_earning' => 'Rs.'.$totalEarnings.'.00',
            ];
        }

        $this->partnersInfo = $partnersInfo;
    }

    public function render()
    {
        return view('livewire.earning.index')->layout('layouts.app');
    }
}
