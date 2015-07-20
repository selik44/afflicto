@extends('front.layout')

@section('title')
    Success - Checkout - @parent
@stop

@section('article')
	<h2>@lang('store.done')</h2>
    <p class="lead">Your order has been received!</p>
@stop