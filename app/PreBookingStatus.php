<?php

namespace App;

enum PreBookingStatus: string
{
    case PENDING_PAYMENT = 'pending_payment';
    // Legacy name for an unfinished checkout, still stored on older rows.
    // Kept so those rows load instead of throwing; new rows use PENDING_PAYMENT.
    case LEGACY_PENDING = 'pending';
    case PAID = 'paid';
    case CONVERTED = 'converted';
    case FAILED = 'failed';
}
