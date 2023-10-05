<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Tickets') }}
    </h2>
</x-slot>
<div class="py-12">
    <div class="max-w-screen-xl px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            @if (count($tickets) === 0)
                <div class="flex justify-center mt-16 px-0">
                    <p class="text-gray-500 dark:text-gray-400 text-lg">You haven't purchased any tickets yet.</p>
                </div>
            @else
                @foreach ($tickets as $ticket)
                    <a href=""
                       class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div
                                class="h-16 w-16 flex items-center justify-center rounded-full">
                                <svg class="w-7 h-7 text-gray-800 dark:text-white" aria-hidden="true"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6.072 10.072 2 2 6-4m3.586 4.314.9-.9a2 2 0 0 0 0-2.828l-.9-.9a2 2 0 0 1-.586-1.414V5.072a2 2 0 0 0-2-2H13.8a2 2 0 0 1-1.414-.586l-.9-.9a2 2 0 0 0-2.828 0l-.9.9a2 2 0 0 1-1.414.586H5.072a2 2 0 0 0-2 2v1.272a2 2 0 0 1-.586 1.414l-.9.9a2 2 0 0 0 0 2.828l.9.9a2 2 0 0 1 .586 1.414v1.272a2 2 0 0 0 2 2h1.272a2 2 0 0 1 1.414.586l.9.9a2 2 0 0 0 2.828 0l.9-.9a2 2 0 0 1 1.414-.586h1.272a2 2 0 0 0 2-2V13.8a2 2 0 0 1 .586-1.414Z"/>
                                </svg>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">{{ $ticket->ticket->event->event_name}}</h2>

                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                {{ $ticket->ticket->event->event_description ?? 'No description available' }}
                            </p>

                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Date: {{ date('M d, Y', strtotime($ticket->ticket->event->event_start_date)) }}
                                - {{ date('M d, Y', strtotime($ticket->ticket->event->event_end_date)) }}
                            </p>

                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Location: {{ $ticket->ticket->event->event_location }}
                            </p>

                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Tickets Available: {{ $ticket->ticket->available_quantity }}
                                / {{$ticket->ticket->allocated_seats }}
                            </p>

                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Ticket Price: Rs.{{ number_format($ticket->ticket->ticket_price, 2) }}
                            </p>

                            <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Purchased Date: {{ date('M d, Y', strtotime($ticket->created_at)) }}
                            </p>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</div>
