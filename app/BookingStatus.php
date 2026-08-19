<?php

namespace App;

enum BookingStatus: string
{
    case INTAKE_COMPLETE = 'intake_complete';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
