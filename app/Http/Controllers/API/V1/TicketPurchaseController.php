<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketPurchaseController extends Controller
{
    /**
     * Purchase a ticket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchase(Request $request, int $eventId)
    {
        // Find the ticket by its ID
        $ticket = Ticket::where('event_id', $eventId)->first();

        // Check if the ticket is available for purchase
        if ($ticket->available_quantity <= 0) {
            return response()->json(['message' => 'Ticket is sold out.'], 400);
        }

        // Reduce the available quantity of the ticket
        $ticket->decrement('available_quantity');

        TicketPurchase::create([
            'user_id' => auth()->user()->id,
            'ticket_id' => $ticket->id,
            'purchase_date' => now(),
        ]);

        return response()->json([
            'message' => 'Ticket purchased successfully.',
            'ticket' => $ticket,
        ], 200);
    }

    /**
     * Get earnings for a specific event.
     *
     * @param  int  $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function earningsByEvent(int $eventId)
    {
        // Find the event by its ID
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        // Calculate earnings for the event
        $earnings = DB::table('tickets')
            ->where('event_id', $eventId)
            ->sum(DB::raw('ticket_price * (allocated_seats - available_quantity)'));

        return response()->json(['earnings' => $earnings], 200);
    }

    /**
     * Get earnings for a specific partner.
     *
     * @param  int  $partnerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function earningsByPartner(int $partnerId)
    {
        // Find the partner by its ID
        $partner = Partner::find($partnerId);

        if (!$partner) {
            return response()->json(['message' => 'Partner not found.'], 404);
        }

        // Retrieve earnings with event details for each event
        $events = DB::table('events')
            ->where('partner_id', $partnerId)
            ->get();

        $earningsByEvent = [];
        foreach ($events as $event) {
            $earnings = DB::table('tickets')
                ->where('event_id', $event->id)
                ->sum(DB::raw('ticket_price * (allocated_seats - available_quantity)'));

            // Include event details along with earnings
            $earningsByEvent[] = [
                'event' => [
                    'id' => $event->id,
                    'name' => $event->event_name,
                    'description' => $event->event_description,
                    'start_date' => $event->event_start_date,
                    'end_date' => $event->event_end_date,
                    'location' => $event->event_location,
                ],
                'earning' => $earnings,
            ];
        }

        return response()->json(['earnings_by_event' => $earningsByEvent], 200);
    }

    /**
     * Get earnings for the currently authenticated partner.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOwnEarnings()
    {
        // Retrieve the currently authenticated user's partner ID
        $partnerId = Auth::user()->partner->id;

        // Find the partner by its ID
        $partner = Partner::find($partnerId);

        if (!$partner) {
            return response()->json(['message' => 'Partner not found.'], 404);
        }

        // Retrieve earnings with event details for each event
        $events = DB::table('events')
            ->where('partner_id', $partnerId)
            ->get();

        $earningsByEvent = [];
        foreach ($events as $event) {
            $earnings = DB::table('tickets')
                ->where('event_id', $event->id)
                ->sum(DB::raw('ticket_price * (allocated_seats - available_quantity)'));

            // Include event details along with earnings
            $earningsByEvent[] = [
                'event' => [
                    'id' => $event->id,
                    'name' => $event->event_name,
                    'description' => $event->event_description,
                    'start_date' => $event->event_start_date,
                    'end_date' => $event->event_end_date,
                    'location' => $event->event_location,
                ],
                'earning' => $earnings,
            ];
        }

        return response()->json(['earnings_by_event' => $earningsByEvent], 200);
    }

    /**
     * Get tickets of the currently authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPurchasedTickets()
    {
        // Retrieve the currently authenticated user's ID
        $userId = auth()->user()->id;

        // Query the TicketPurchase model to get purchased tickets by the user
        $purchasedTickets = TicketPurchase::where('user_id', $userId)
            ->with('ticket.event') // Include the ticket and event details in the result
            ->get();

        return response()->json(['purchased_tickets' => $purchasedTickets], 200);
    }

    /**
     * Get total earnings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function totalEarnings()
    {
        // Retrieve earnings with event details for each event
        $events = Event::all();

        $earningsByEvent = [];
        foreach ($events as $event) {
            $earnings = DB::table('tickets')
                ->where('event_id', $event->id)
                ->sum(DB::raw('ticket_price * (allocated_seats - available_quantity)'));

            // Include event details along with earnings
            $earningsByEvent[] = [
                'event' => [
                    'id' => $event->id,
                    'name' => $event->event_name,
                    'description' => $event->event_description,
                    'start_date' => $event->event_start_date,
                    'end_date' => $event->event_end_date,
                    'location' => $event->event_location,
                ],
                'earning' => $earnings,
            ];
        }

        return response()->json(['earnings_by_event' => $earningsByEvent], 200);
    }
}
