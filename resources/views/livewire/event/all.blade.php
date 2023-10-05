<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Events') }}
    </h2>
</x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div class="flex items-center justify-between pb-4 bg-white dark:bg-gray-900 px-2">
                    <div>
                        <x-wireui-button positive label="create" wire:click="create"/>
                    </div>
                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative">
                        <x-wireui-input wire:model.live="search" placeholder="Search Events"/>
                    </div>
                </div>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Event name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Event Location
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Event Start Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Event End Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Tickets Available
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($events as $event)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{$event->event_name}}
                            </th>
                            <td class="px-6 py-4">
                                {{$event->event_location}}
                            </td>
                            <td class="px-6 py-4">
                                {{ date('M d, Y', strtotime($event->event_start_date)) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ date('M d, Y', strtotime($event->event_end_date)) }}
                            </td>
                            <td class="px-6 py-4">
                                {{$event->tickets[0]->available_quantity}} / {{$event->tickets[0]->allocated_seats}}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if ($event->is_active == 1)
                                        <div class="h-2.5 w-2.5 rounded-full bg-green-500 mr-2"></div>Active
                                    @else
                                        <div class="h-2.5 w-2.5 rounded-full bg-red-500 mr-2"></div>Inactive
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-wireui-button secondary wire:click="edit({{ $event->id }})" label="Edit"/>
                                @if(auth()->user()->role->name === 'admin')
                                    <x-wireui-button negative wire:click="deleteConfirmation({{ $event->id }})"
                                                     label="Delete"/>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <x-wireui-modal.card blur title="Event" blur wire:model.defer="formModal">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if(auth()->user()->role->name === 'admin')
                    <div class="col-span-1 sm:col-span-2">
                        <x-wireui-select
                            label="Search a partner"
                            wire:model.defer="partner_id"
                            placeholder="Select a partner"
                        >
                            @foreach($partners as $partner)
                                <x-wireui-select.option value="{{$partner->id}}" label="{{$partner->user->name}}"/>
                            @endforeach
                        </x-wireui-select>
                    </div>
                @endif
                <x-wireui-input label="Name" wire:model="event_name" placeholder="Event name"/>
                <x-wireui-input label="Location" wire:model="event_location" placeholder="Event location"/>
                <div class="col-span-1 sm:col-span-2">
                    <x-wireui-textarea label="Description" wire:model="event_description"
                                       placeholder="Event description"/>
                </div>
                <x-wireui-datetime-picker label="Event start date" wire:model="event_start_date"
                                          placeholder="Event start date" parse-format="YYYY-MM-DD HH:mm:ss"/>
                <x-wireui-datetime-picker label="Event end date" wire:model="event_end_date"
                                          placeholder="Event end date" parse-format="YYYY-MM-DD HH:mm:ss"/>
                <x-wireui-input label="Ticket price" wire:model="ticket_price" placeholder="Ticket price"/>
                <x-wireui-inputs.number label="Allocated seats" wire:model="allocated_seats"
                                        placeholder="Allocated seats"/>
                <x-wireui-datetime-picker label="Ticket sale start date" wire:model="sale_start_date"
                                          placeholder="Ticket sale start date" parse-format="YYYY-MM-DD HH:mm:ss"/>
                <x-wireui-datetime-picker label="Ticket sale end date" wire:model="sale_end_date"
                                          placeholder="Ticket sale end date" parse-format="YYYY-MM-DD HH:mm:ss"/>
            </div>

            <x-slot name="footer">
                <div class="flex justify-between gap-x-4">
                    <x-wireui-button primary label="Save" wire:click="store"/>
                </div>
            </x-slot>
        </x-wireui-modal.card>
    </div>
</div>
