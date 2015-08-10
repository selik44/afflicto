@extends('emails.master')

@section('header')
    <h3 class="end">@lang('emails.order_updated.subject', ['id' => $event->order->id])</h3>
@stop

@section('content')
    <h5>Kommentar: </h5>
    <p>
        {{$event->comment}}
    </p>
@stop

@section('footer')
    @lang('emails.order_updated.footer') <a href="{{route('user')}}">@lang('store.here')</a>.
@stop