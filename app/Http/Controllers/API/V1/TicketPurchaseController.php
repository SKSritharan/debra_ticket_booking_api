<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Ticket;
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

        // You can add additional logic here, such as storing the purchase in your database

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

        // Calculate earnings for the partner
        $earnings = DB::table('tickets')
            ->join('events', 'tickets.event_id', '=', 'events.id')
            ->where('events.partner_id', $partnerId)
            ->sum(DB::raw('ticket_price * (allocated_seats - available_quantity)'));

        return response()->json(['earnings' => $earnings], 200);
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

        // Calculate earnings for the partner
        $earnings = DB::table('tickets')
            ->join('events', 'tickets.event_id', '=', 'events.id')
            ->where('events.partner_id', $partnerId)
            ->sum(DB::raw('ticket_price * (allocated_seats - available_quantity)'));

        return response()->json(['earnings' => $earnings], 200);
    }

    /**
     * Get total earnings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function totalEarnings()
    {
        // Calculate total earnings across all events and partners
        $earnings = DB::table('tickets')
            ->sum(DB::raw('ticket_price * (allocated_seats - available_quantity)'));

        return response()->json(['total_earnings' => $earnings], 200);
    }
}
