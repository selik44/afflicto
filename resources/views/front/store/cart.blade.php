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
        <div class="module-content" style="padding: 0px;">
            <div class="row end">
                <?php
                    $tag = \Friluft\Tag::where('type', '=', 'checkout')->first();
                ?>
                @if($tag)
                    @foreach($tag->products as $p)
                        <div class="col-xs-12 tight">
                            <a href="#" style="width: 100%; display: block; height: 140px; background-image: url('{{$p->getImageURL()}}'); background-position: center; background-size: cover">

                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@stop

@section('article')
    @include('front.partial.cart-table')
    <hr>
    <a class="pull-right button huge success" href="{{route('store.checkout')}}"><i class="fa fa-chevron-right"></i> @lang('store.to checkout')</a>
@stop