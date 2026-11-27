@extends('adminos.layout')

@section('title', $title)
@section('heading', $title)

@section('content')
  <section class="card card-pad" style="max-width:720px">
    <span class="pill neutral">Not built yet</span>
    <p style="font-size:17px;margin:16px 0 8px">{{ $summary }}</p>
    <p class="muted" style="margin:0">Arrives in {{ $phase }}. The data for this page comes from the Cavalcare and CavalPro apps, which still run on sample data, so nothing here is shown until it is real.</p>
  </section>
@endsection
