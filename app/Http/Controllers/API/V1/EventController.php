<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('tickets')->get();
        return response()->json(['events' => $events], 200);
    }

    public function store(Request $request)
    {
        $partner = auth()->user()->partner;

        // Validate the request data
        $validatedEventData = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'event_location' => 'required|string|max:255',
        ]);

        $validatedTicketData = $request->validate([
            'ticket_price' => 'required|numeric|min:0',
            'allocated_seats' => 'required|integer|min:1',
            'sale_start_date' => 'required|date',
            'sale_end_date' => 'required|date|after_or_equal:sale_start_date',
        ]);

        // Create a new event
        $event = $partner->events()->create($validatedEventData);
        $ticketData = array_merge($validatedTicketData, ['available_quantity' => $validatedTicketData['allocated_seats']]);
        $ticket = $event->tickets()->create($ticketData);

        return response()->json([
            'success' => true,
            'message' => 'Event and ticket created successfully.',
            'event' => $event,
            'ticket' => $ticket,
        ], 201);
    }

    public function show(string $id)
    {
        $event = Event::with('tickets')->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        return response()->json(['event' => $event], 200);
    }

    public function update(Request $request, string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'event_name' => 'string|max:255',
            'event_description' => 'nullable|string',
            'event_start_date' => 'date',
            'event_end_date' => 'date|after_or_equal:event_start_date',
            'event_location' => 'string|max:255',
        ]);

        // Update the event
        $event->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully.',
            'event' => $event
        ], 200);
    }

    public function destroy(string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        // Inactivate the event
        $event->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Event inactivated successfully.',
            'event' => $event
        ], 200);
    }
}
