@php
    $carePlanId = $booking['booking_details']['care_plan_id'] ?? null;
    $carePlanName = $carePlanId 
        ? \App\Models\CarePlan::find($carePlanId)?->name 
        : 'N/A';
@endphp

<x-filament-panels::page>

    {{-- Include FullCalendar and Font Awesome --}}
    @push('styles')
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <style>
            /* Custom calendar styling */
            #calendar {
                font-family: inherit;
            }
            
            .fc-event {
                cursor: pointer;
                border-radius: 4px;
            }
            
            .fc-daygrid-day:hover {
                background-color: rgba(59, 130, 246, 0.05);
                cursor: pointer;
            }
            
            .fc-daygrid-day.fc-day-today {
                background-color: rgba(59, 130, 246, 0.1) !important;
            }
            
            .fc .fc-button-primary {
                background-color: rgb(59, 130, 246);
                border-color: rgb(59, 130, 246);
            }
            
            .fc .fc-button-primary:hover {
                background-color: rgb(37, 99, 235);
                border-color: rgb(37, 99, 235);
            }
        </style>
    @endpush

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
    @endpush

    <div class="space-y-6">
        
        {{-- Calendar - This stays visible always --}}
        <div wire:key="calendar-container" class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div
                    x-data="calendarComponent()"
                    x-init="initCalendar(@js($this->getBookingsForCalendar()))"
                    wire:ignore
                >
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        {{-- Booking Summary (appears below calendar when date is clicked) --}}
        @if($selectedDate)
            <div 
                wire:key="bookings-{{ $selectedDate }}"
                x-data="{ show: true }"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden"
            >
                {{-- Header with gradient background --}}
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white bg-opacity-20 rounded-lg p-2">
                                <i class="fas fa-calendar-day text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">
                                    Bookings Overview
                                </h2>
                                <p class="text-blue-100 text-sm mt-0.5">
                                    {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}
                                </p>
                            </div>
                        </div>
                        <button 
                            wire:click="clearSelection" 
                            type="button"
                            class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-2 text-white transition-all"
                            title="Close"
                        >
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    @if(count($selectedDateBookings) > 0)
                        {{-- Summary stats bar --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            {{-- Total Bookings --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-blue-500 rounded-lg p-3">
                                        <i class="fas fa-users text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">Total Bookings</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ count($selectedDateBookings) }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Active Bookings --}}
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-500 rounded-lg p-3">
                                        <i class="fas fa-check-circle text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-green-600 uppercase tracking-wider">Active</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-1">
                                            {{ collect($selectedDateBookings)->filter(function($booking) {
                                                return isset($booking['booking_details']['status']) && $booking['booking_details']['status'] === 'confirmed';
                                            })->count() }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Care Plans --}}
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-purple-500 rounded-lg p-3">
                                        <i class="fas fa-clipboard-list text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-purple-600 uppercase tracking-wider">Care Plans</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-1">
                                            {{ collect($selectedDateBookings)->pluck('booking_details.care_plan_id')->unique()->count() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bookings list --}}
                        <div class="space-y-4">
                            @foreach ($selectedDateBookings as $index => $booking)
                                <div wire:key="booking-{{ $booking['id'] }}-{{ $index }}" 
                                     class="bg-gray-50 border border-gray-200 rounded-lg p-5 hover:shadow-md hover:border-blue-300 transition-all duration-200">
                                    
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0 lg:space-x-6">
                                        
                                        {{-- Patient Info Section --}}
                                        <div class="flex items-center space-x-4 flex-1">
                                            {{-- Avatar --}}
                                            <div class="flex-shrink-0">
                                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                                    {{ substr($booking['patient_details']['full_name'] ?? 'N', 0, 1) }}
                                                </div>
                                            </div>
                                            
                                            {{-- Patient Details --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <h3 class="text-lg font-bold text-gray-900 truncate">
                                                        {{ $booking['patient_details']['full_name'] ?? 'N/A' }}
                                                    </h3>
                                                    @if(isset($booking['booking_details']['status']))
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                            {{ $booking['booking_details']['status'] === 'confirmed' 
                                                                ? 'bg-green-100 text-green-700 border border-green-300' 
                                                                : 'bg-yellow-100 text-yellow-700 border border-yellow-300' }}">
                                                            <i class="fas fa-circle text-xs mr-1.5"></i>
                                                            {{ ucfirst($booking['booking_details']['status']) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                {{-- Info Grid --}}
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                                                    {{-- Care Plan --}}
                                                    <div class="flex items-center space-x-2">
                                                        <div class="bg-blue-100 rounded-lg p-2">
                                                            <i class="fas fa-file-medical text-blue-600 text-sm"></i>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs text-gray-500 font-medium">Care Plan</p>
                                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                            {{ $carePlanName }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Duration --}}
                                                    <div class="flex items-center space-x-2">
                                                        <div class="bg-purple-100 rounded-lg p-2">
                                                            <i class="fas fa-calendar-alt text-purple-600 text-sm"></i>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs text-gray-500 font-medium">Duration</p>
                                                            <p class="text-sm font-semibold text-gray-900">
                                                                {{ isset($booking['booking_details']['start_date']) ? \Carbon\Carbon::parse($booking['booking_details']['start_date'])->format('M j') : '' }}
                                                                <i class="fas fa-arrow-right text-xs mx-1 text-gray-400"></i>
                                                                {{ isset($booking['booking_details']['end_date']) ? \Carbon\Carbon::parse($booking['booking_details']['end_date'])->format('M j, Y') : '' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Action Button --}}
                                        <div class="flex-shrink-0 lg:ml-4">
                                            <a 
                                                href="{{ route('filament.admin.resources.bookings.edit', $booking['id']) }}" 
                                                class="inline-flex items-center justify-center space-x-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                                            >
                                                <i class="fas fa-eye"></i>
                                                <span>View Details</span>
                                                <i class="fas fa-arrow-right text-xs"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Footer with total count --}}
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600 text-center">
                                Showing <span class="font-bold text-gray-900">{{ count($selectedDateBookings) }}</span> 
                                {{ Str::plural('booking', count($selectedDateBookings)) }} for this date
                            </p>
                        </div>

                    @else
                        {{-- Empty state --}}
                        <div class="text-center py-16">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                                <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-700 mb-2">No bookings on this date</h3>
                            <p class="text-gray-500 text-sm">Select another date to view bookings</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>

    {{-- Calendar Script --}}
    @script
    <script>
        Alpine.data('calendarComponent', () => ({
            calendar: null,
            
            initCalendar(events) {
                this.$nextTick(() => {
                    const calendarEl = document.getElementById('calendar');
                    
                    if (!calendarEl) {
                        console.error('Calendar element not found');
                        return;
                    }

                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,dayGridWeek'
                        },
                        events: events,
                        dateClick: (info) => {
                            info.jsEvent.preventDefault();
                            info.jsEvent.stopPropagation();
                            @this.call('dateClicked', info.dateStr);
                        },
                        eventClick: (info) => {
                            info.jsEvent.preventDefault();
                            info.jsEvent.stopPropagation();
                            @this.call('dateClicked', info.event.startStr);
                        },
                        height: 'auto',
                        eventDisplay: 'block',
                        displayEventTime: false,
                        eventColor: '#3b82f6',
                        eventBackgroundColor: '#3b82f6',
                        eventBorderColor: '#2563eb'
                    });

                    this.calendar.render();
                });
            }
        }));
    </script>
    @endscript
</x-filament-panels::page>