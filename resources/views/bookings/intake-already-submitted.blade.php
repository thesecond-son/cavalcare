@extends('layouts.app')

@section('title', 'Care Details Already Submitted — CavalCare')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700&family=Cabin:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════
   ALREADY SUBMITTED PAGE
═══════════════════════════════════════════════ */

@keyframes iconPop {
  0% {
    opacity: 0;
    transform: scale(.6);
  }

  70% {
    transform: scale(1.08);
  }

  100% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes intakeFadeUp {
  from {
    opacity: 0;
    transform: translateY(24px);
  }

  to {
    opacity: 1;
    transform: none;
  }
}

/* ─────────────────────────────────────────────
   PAGE WRAPPER
───────────────────────────────────────────── */

.intake-page {
  min-height: calc(100svh - 64px);

  /*
   * Your navbar is fixed, so deliberately create
   * breathing room underneath it.
   */
  padding:
    116px 20px
    80px;

  display: flex;
  align-items: center;
  justify-content: center;

  position: relative;
  overflow: hidden;

  background:
    radial-gradient(
      circle at 15% 20%,
      rgba(4,169,167,.07),
      transparent 32%
    ),
    radial-gradient(
      circle at 85% 75%,
      rgba(255,205,173,.13),
      transparent 32%
    ),
    #fff;
}

/*
 * Subtle background decorative elements.
 * These make the desktop page feel intentionally
 * designed rather than like a small card floating
 * between a navbar and footer.
 */
.intake-page::before,
.intake-page::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(2px);
}

.intake-page::before {
  width: 360px;
  height: 360px;
  top: 12%;
  left: -180px;
  background: rgba(4,169,167,.045);
}

.intake-page::after {
  width: 420px;
  height: 420px;
  bottom: -220px;
  right: -180px;
  background: rgba(255,205,173,.09);
}


/* ─────────────────────────────────────────────
   MAIN CARD
───────────────────────────────────────────── */

.intake-card {
  position: relative;
  z-index: 1;

  width: min(100%, 620px);

  background: rgba(255,255,255,.97);

  border: 1px solid rgba(26,58,47,.08);
  border-radius: var(--r-xl);

  box-shadow:
    0 24px 70px rgba(26,58,47,.11),
    0 4px 16px rgba(26,58,47,.04);

  padding: 58px 56px 52px;

  text-align: center;

  animation: intakeFadeUp .65s var(--out) both;
}


/* ─────────────────────────────────────────────
   SUCCESS ICON
───────────────────────────────────────────── */

.intake-icon {
  width: 82px;
  height: 82px;

  margin: 0 auto 26px;

  border-radius: 50%;

  background: rgba(4,169,167,.11);
  color: var(--indigo);

  display: flex;
  align-items: center;
  justify-content: center;

  box-shadow:
    0 0 0 10px rgba(4,169,167,.035);

  animation: iconPop .55s var(--spring) both;
}

.intake-icon i {
  font-size: 38px;
}


/* ─────────────────────────────────────────────
   TITLE
───────────────────────────────────────────── */

.intake-title {
  font-family: 'Nanum Myeongjo', serif;

  font-size: clamp(1.9rem, 4vw, 2.55rem);

  font-weight: 400;
  line-height: 1.13;

  letter-spacing: -.03em;

  color: var(--ocean);

  margin: 0 0 18px;
}


/* ─────────────────────────────────────────────
   DESCRIPTION
───────────────────────────────────────────── */

.intake-text {
  max-width: 470px;

  margin: 0 auto 32px;

  font-size: .98rem;
  line-height: 1.75;

  color: var(--muted);
}


/* ─────────────────────────────────────────────
   BOOKING DETAILS
───────────────────────────────────────────── */

.booking-box {
  background: var(--cream);

  border: 1px solid rgba(26,58,47,.05);

  border-radius: var(--r-md);

  padding: 20px 22px;

  margin-bottom: 20px;

  text-align: left;
}

.booking-row {
  display: flex;
  align-items: center;
  justify-content: space-between;

  gap: 24px;

  font-size: .88rem;
  line-height: 1.5;
}

.booking-row + .booking-row {
  margin-top: 13px;
  padding-top: 13px;

  border-top: 1px solid rgba(26,58,47,.07);
}

.booking-label {
  color: var(--muted);
  flex-shrink: 0;
}

.booking-value {
  color: var(--ocean);

  font-weight: 600;

  text-align: right;

  max-width: 65%;
}


/* ─────────────────────────────────────────────
   NEXT STEPS
───────────────────────────────────────────── */

.info-box {
  background: #f8fbf6;

  border: 1px solid rgba(26,58,47,.07);

  border-radius: var(--r-md);

  padding: 19px 20px;

  margin-bottom: 28px;

  text-align: left;
}

.info-box p {
  margin: 0;

  font-size: .87rem;
  line-height: 1.7;

  color: var(--muted);
}

.info-box strong {
  color: var(--ocean);
  font-weight: 600;
}


/* ─────────────────────────────────────────────
   WHATSAPP BUTTON
───────────────────────────────────────────── */

.conf-wa {
  display: inline-flex;

  align-items: center;
  justify-content: center;

  gap: 9px;

  min-height: 48px;

  background: var(--ocean);
  color: #fff;

  text-decoration: none;

  font-family: 'Cabin', sans-serif;
  font-size: .9rem;
  font-weight: 600;

  padding: 13px 24px;

  border-radius: var(--r-sm);

  box-shadow: 0 5px 18px rgba(26,58,47,.15);

  transition:
    background .18s,
    transform .22s var(--spring),
    box-shadow .22s;
}

.conf-wa i {
  font-size: 17px;
}

.conf-wa:hover {
  background: var(--ocean-h);
  color: #fff;

  transform: translateY(-3px);

  box-shadow: 0 9px 26px rgba(26,58,47,.22);
}


/* ─────────────────────────────────────────────
   BOOKING REFERENCE
───────────────────────────────────────────── */

.reference {
  margin-top: 22px;

  font-size: .75rem;

  color: var(--muted);

  letter-spacing: .01em;
}

.reference strong {
  color: var(--ocean);

  font-weight: 600;
}


/* ═══════════════════════════════════════════════
   TABLET
═══════════════════════════════════════════════ */

@media (max-width: 767px) {

  .intake-page {
    min-height: calc(100svh - 60px);

    padding:
      92px 18px
      64px;
  }

  .intake-card {
    width: 100%;

    padding: 46px 30px 40px;

    border-radius: var(--r-lg);
  }

  .intake-icon {
    width: 72px;
    height: 72px;

    margin-bottom: 22px;
  }

  .intake-icon i {
    font-size: 34px;
  }

  .intake-title {
    font-size: clamp(1.75rem, 7vw, 2.25rem);
  }

  .intake-text {
    font-size: .94rem;
    margin-bottom: 26px;
  }
}


/* ═══════════════════════════════════════════════
   MOBILE
═══════════════════════════════════════════════ */

@media (max-width: 479px) {

  .intake-page {
    /*
     * Don't vertically center on very small screens.
     * Let the content flow naturally so the page
     * doesn't feel cramped around the navbar/footer.
     */
    align-items: flex-start;

    padding:
      84px 16px
      52px;
  }

  .intake-card {
    padding: 36px 20px 32px;

    border-radius: var(--r-lg);

    box-shadow:
      0 16px 44px rgba(26,58,47,.09);
  }

  .intake-icon {
    width: 66px;
    height: 66px;

    margin-bottom: 20px;
  }

  .intake-icon i {
    font-size: 31px;
  }

  .intake-title {
    font-size: clamp(1.65rem, 8vw, 2rem);

    line-height: 1.16;

    margin-bottom: 14px;
  }

  .intake-text {
    font-size: .9rem;
    line-height: 1.7;

    margin-bottom: 24px;
  }

  .booking-box {
    padding: 16px 15px;
  }

  .booking-row {
    align-items: flex-start;

    gap: 12px;

    font-size: .82rem;
  }

  .booking-value {
    max-width: 62%;
    overflow-wrap: anywhere;
  }

  .info-box {
    padding: 16px 15px;

    margin-bottom: 24px;
  }

  .info-box p {
    font-size: .82rem;
  }

  .conf-wa {
    width: 100%;

    padding: 14px 16px;

    font-size: .86rem;
  }

  .reference {
    margin-top: 19px;

    font-size: .7rem;
  }
}


/* ═══════════════════════════════════════════════
   REDUCED MOTION
═══════════════════════════════════════════════ */

@media (prefers-reduced-motion: reduce) {
  .intake-card,
  .intake-icon {
    animation: none;
  }

  .conf-wa {
    transition: none;
  }
}
</style>
@endpush

@section('content')

<main class="intake-page">

  <div class="intake-card">

    <div class="intake-icon">
      <i class="bi bi-check2-circle"></i>
    </div>

    <h1 class="intake-title">
      Your care details<br>
      have already been submitted.
    </h1>

    <p class="intake-text">
      We've already received the care information for this booking.
      There's no need to complete the form again.
    </p>

    <div class="booking-box">

      <div class="booking-row">
        <span class="booking-label">Care Plan</span>

        <span class="booking-value">
          {{ $booking->carePlan?->name ?? '—' }}
        </span>
      </div>

      <div class="booking-row">
        <span class="booking-label">Care Type</span>

        <span class="booking-value">
          {{ $booking->careType?->name ?? '—' }}
        </span>
      </div>

      <div class="booking-row">
        <span class="booking-label">Status</span>

        <span class="booking-value">
          Details Received
        </span>
      </div>

    </div>

    <div class="info-box">

      <p>
        <strong>What happens next?</strong><br>

        Our care team will review the information you've provided
        and contact you regarding the next steps for arranging care.
      </p>

    </div>

    <a
      href="https://wa.me/{{ config('app.whatsapp_number') }}"
      class="conf-wa"
    >
      <i class="bi bi-whatsapp"></i>
      Questions? Message us on WhatsApp
    </a>

    <div class="reference">
        Booking reference:
        <strong>{{ $booking->reference }}</strong>
    </div>

  </div>

</main>

@endsection