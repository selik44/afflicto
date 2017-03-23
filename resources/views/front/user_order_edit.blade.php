@extends('front.layout')

@section('title')
    @lang('store.order') - @lang('store.my account') - @parent
@stop

@section('aside')
    @include('front.partial.user_menu')
@stop

@section('article')
    order date {{ $order->created_at->format('d M Y') }}
    <br>
    Order # {{ $order->id }}
    <br>
    Product name {{ $product->name }}

    {!! Form::open() !!}
    Title
    <br>
    {!! Form::text('title', isset($review) ? $review->title : "") !!}
    {!! Form::textarea('comment', isset($review) ? $review->comment : "" )!!}
    {!! Form::number('rating', isset($review) ? $review->rating : "") !!}
    {!! Form::submit(trans('store.order.review.update')) !!}
    <a href="{{ route('user.order', $order->id) }}">{{ trans('store.order.review.back') }}</a>
    {!! Form::close() !!}
@stop

