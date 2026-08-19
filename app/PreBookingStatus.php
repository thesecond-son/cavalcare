<?php

namespace App;

enum PreBookingStatus: string
{
    case PENDING_PAYMENT = 'pending_payment';
    case PAID = 'paid';
    case CONVERTED = 'converted';
}
