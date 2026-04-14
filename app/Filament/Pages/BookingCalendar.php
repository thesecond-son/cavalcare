<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\On;


class BookingCalendar extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'Booking Management';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDateRange;
    protected string $view = 'filament.pages.booking-calendar';

    public array $selectedDateBookings = [];
    public ?string $selectedDate = null;

    // Remove the old $listeners property and use the new Livewire v3 attribute
    
    public function getBookingsForCalendar(): array
    {
        return Booking::query()
            ->select('id', 'booking_details', 'patient_details')
            ->get()
            ->flatMap(function ($booking) {
                $dates = [];
            
                $start = Carbon::parse($booking->booking_details['start_date']);
                $end   = Carbon::parse($booking->booking_details['end_date']);
            
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $dates[] = [
                        'title' => 'Booked',
                        'start' => $date->toDateString(),
                        'allDay' => true,
                        'backgroundColor' => '#3b82f6',
                        'borderColor' => '#2563eb',
                    ];
                }
            
                return $dates;
            })            
            ->toArray();
    }

    #[On('dateClicked')]
    public function dateClicked($date)
    {
        $this->selectedDate = $date;

        $this->selectedDateBookings = Booking::query()
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(booking_details, '$.start_date')) <= ?",
                [$date]
            )
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(booking_details, '$.end_date')) >= ?",
                [$date]
            )
            ->get()
            ->toArray();
    }

    public function clearSelection()
    {
        $this->selectedDate = null;
        $this->selectedDateBookings = [];
    }
}