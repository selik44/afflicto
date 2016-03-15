@extends('front.layout')

@section('breadcrumbs', '')

@section('article')
    <?php
        $tile = function($image) {
	        if (!$image->name) return;
            $str = '<a class="tile" href="' .$image->data['link'] .'">';
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
            <h4 class="end">Populære</h4>
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
            <h4 class="end">Nyheter</h4>
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

	    <div class="row tiles bottom2 clearfix paper">
		    <div class="left">
			    <div class="top">
				    {!! $tile($images['bottom_2_top_left']) !!}
			    </div>
			    <div class="bottom">
				    {!! $tile($images['bottom_2_1']) !!}
				    {!! $tile($images['bottom_2_2']) !!}
			    </div>
		    </div>
		    <div class="right">
			    <div class="top">
				    {!! $tile($images['bottom_2_top_right']) !!}
			    </div>
			    <div class="bottom">
				    {!! $tile($images['bottom_2_3']) !!}
				    {!! $tile($images['bottom_2_4']) !!}
			    </div>
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
            delay: 6500,
            transitionSpeed: 850,
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