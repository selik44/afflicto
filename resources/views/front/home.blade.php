@extends('front.layout')

@section('breadcrumbs', '')

@section('article')
    <?php
        $tile = function($image) {
            $str = '<a class="tile" href="' .$image['data']['link'] .'">';
            $str .= '<img src="' .asset('images/' .$image->name) .'">';
            $str .= '</a>';
            return $str;
        };
    ?>
    <div id="home" class="clearfix">
        <div class="row top clearfix paper">
            @include('front.partial.slider')
            <div class="tiles">
                {!! $tile($images['top_1']) !!}
                {!! $tile($images['top_2']) !!}
            </div>
        </div>

        <div class="row products clearfix paper">
            <h4>Populære</h4>
            <div class="products-grid">
            @foreach($popular as $product)
                @include('front.partial.products-block', ['product' => $product])
            @endforeach
            </div>
        </div>

        <div class="row tiles middle clearfix paper">
            <div class="left">
                <div class="top">
                    {!! $tile($images['middle_top_left']) !!}
                </div>
                <div class="bottom">
                    {!! $tile($images['middle_bottom_left_1']) !!}
                    {!! $tile($images['middle_bottom_left_2']) !!}
                </div>
            </div>

            <div class="right">
                <div class="top">
                    {!! $tile($images['middle_top_right']) !!}
                </div>
                <div class="bottom">
                    {!! $tile($images['middle_bottom_right_1']) !!}
                    {!! $tile($images['middle_bottom_right_2']) !!}
                </div>
            </div>
        </div>

        <div class="row products clearfix paper">
            <h4>Nyheter</h4>
            <div class="products-grid">
            @foreach($news as $product)
                @include('front.partial.products-block', ['product' => $product])
            @endforeach
            </div>
        </div>

        <div class="row tiles bottom clearfix paper">
            <div class="left">
                {!! $tile($images['bottom_left']) !!}
            </div>
            <div class="right">
                {!! $tile($images['bottom_right_1']) !!}
                {!! $tile($images['bottom_right_2']) !!}
            </div>
        </div>

        <div class="row tiles paper">
            {!! $tile($images['bottom']) !!}
        </div>
    </div>
@stop

@section('scripts')
    @parent

    <script>
        var home = $("#home");

        home.find('.slider').friluftSlider({
            delay: 4000,
            transitionSpeed: 600,
            autoHeight: true,
            heightRatio: 7 / 16,
            useElements: true,
        });

        // initialize isotope
        imagesLoaded(document.querySelector('.products-grid'), function() {
            $(".products-grid").isotope({
                itemSelector: '.product',
                layoutMode: 'fitRows',
                getSortData: {
                    price: '[data-price] parseInt',
                    manufacturer: '[data-manufacturer]',
                }
            });
        });
    </script>
@stop















@section('articleasd')
	<div class="products-popular paper clearfix" style="padding: 2rem;">
        <h2 class="end">@lang('store.popular products')</h2>

        <div class="row">
		@foreach($popular as $product)
			<div class="col-xs-6 col-l-3">
				@include('front.partial.products-block', ['product' => $product])
			</div>
		@endforeach
        </div>
	</div>

    <br>

	<div class="row paper">
		<div class="col-xs-6 tight">
			<a href="#">
				<img src="http://lorempixel.com/800/600/abstract"/>
			</a>
		</div>

		<div class="col-xs-6 tight">
			<a href="#">
				<img src="http://lorempixel.com/800/600/sports"/>
			</a>
		</div>
	</div>
@stop