@extends('layouts.app')

@section('title', $title ?? 'Something went wrong — CavalCare')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700&family=Cabin:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
    --cream:#F0FFDB;
    --yellow:#04A9A7;
    --ocean:#1A3A2F;
    --muted:#3d6b57;
    --spring:cubic-bezier(.34,1.56,.64,1);
}

*,
*::before,
*::after{
    box-sizing:border-box;
}

body{
    font-family:'Cabin',system-ui,sans-serif;
    background:var(--cream);
    color:var(--ocean);
    -webkit-font-smoothing:antialiased;
}

.error-page{
    min-height:100svh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 20px;
}

.error-card{
    width:100%;
    max-width:520px;
    background:#fff;
    border:1px solid rgba(26,58,47,.08);
    border-radius:24px;
    box-shadow:0 16px 56px rgba(26,58,47,.1);
    padding:52px 40px;
    text-align:center;
}

@media(max-width:479px){
    .error-card{
        padding:38px 24px;
    }
}

.error-icon{
    width:76px;
    height:76px;
    border-radius:50%;
    background:rgba(4,169,167,.12);
    color:var(--yellow);
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 24px;
}

.error-icon i{
    font-size:32px;
}

.error-code{
    font-size:.75rem;
    font-weight:700;
    letter-spacing:.18em;
    text-transform:uppercase;
    color:var(--muted);
    margin-bottom:10px;
}

.error-title{
    font-family:'Nanum Myeongjo',serif;
    font-size:clamp(1.7rem,5vw,2.3rem);
    font-weight:400;
    line-height:1.15;
    margin-bottom:14px;
}

.error-message{
    color:var(--muted);
    font-size:.96rem;
    line-height:1.7;
    max-width:390px;
    margin:0 auto 28px;
}

.error-actions{
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:10px;
}

.error-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:13px 22px;
    border-radius:10px;
    text-decoration:none;
    font-size:.9rem;
    font-weight:600;
    transition:transform .2s var(--spring),
                background .18s;
}

.error-btn-primary{
    background:var(--ocean);
    color:#fff;
}

.error-btn-primary:hover{
    background:#0f5c3d;
    transform:translateY(-2px);
}

.error-btn-secondary{
    background:rgba(26,58,47,.07);
    color:var(--ocean);
}

.error-btn-secondary:hover{
    background:rgba(26,58,47,.12);
    transform:translateY(-2px);
}
</style>
@endpush

@section('content')

<div class="error-page">

    <div class="error-card">

        <div class="error-icon">
            <i class="bi bi-exclamation-circle"></i>
        </div>

        <div class="error-code">
            Error {{ $code ?? '400' }}
        </div>

        <h1 class="error-title">
            {{ $title ?? 'Something went wrong' }}
        </h1>

        <p class="error-message">
            {{ $message ?? 'We were unable to complete your request. Please try again or contact our care team if you need assistance.' }}
        </p>

        <div class="error-actions">

            <a href="{{ route('home') }}" class="error-btn error-btn-primary">
                <i class="bi bi-house"></i>
                Return Home
            </a>

            <a
                href="https://wa.me/{{ config('app.whatsapp_number') }}"
                class="error-btn error-btn-secondary"
            >
                <i class="bi bi-whatsapp"></i>
                Contact Us
            </a>

        </div>

    </div>

</div>

@endsection