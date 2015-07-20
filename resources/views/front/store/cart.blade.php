@extends('front.layout')

@section('title')
    Cart - @parent
@stop

@section('breadcrumbs', Breadcrumbs::render('store.cart'))

@section('intro')
    <div class="checkout-navigation">
        <a class="item {{(Request::route()->getName() == 'store.cart') ? 'current' : ''}}" href="{{route('store.cart')}}">
            <span class="badge">1</span> <span class="title">@lang('store.cart')</span>
        </a>

        <a class="item {{(Request::route()->getName() == 'store.checkout') ? 'current' : ''}}" href="{{route('store.checkout')}}">
            <span class="badge">2</span> <span class="title">@lang('store.checkout')</span>
        </a>

        <div class="item {{(Request::route()->getName() == 'store.success') ? 'current' : ''}}" href="#">
            <span class="badge">3</span> <span class="title">@lang('store.done')</span>
        </div>
    </div>
@stop

@section('aside')
    <div class="block module">
        <div class="module-content">
            <?php
                $tag = \Friluft\Tag::where('type', '=', 'checkout')->first();
            ?>
            @if($tag)
                @foreach($tag->products as $p)
                    @include('front.partial.products-block', ['product' => $p])
                @endforeach
            @endif
        </div>
    </div>
@stop

@section('article')
    @include('front.partial.cart-table')
    <hr>
    <a class="pull-right button huge success" href="{{route('store.checkout')}}"><i class="fa fa-chevron-right"></i> @lang('store.to checkout')</a>
@stop