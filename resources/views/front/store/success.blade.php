@extends('front.layout')

@section('title')
    Success - Checkout - @parent
@stop

@section('article')
    <div class="paper" style="padding: 2rem;">
        <h2>@lang('store.done')</h2>
        {!! $snippet !!}
    </div>
@stop