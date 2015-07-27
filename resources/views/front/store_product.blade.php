@extends('front.layout')


@section('title')
	{{{$product->name}}} - {{{$product->categories->first()->name}}} - @parent
@stop


@section('breadcrumbs', Breadcrumbs::render('product', $product, $category))

@section('article')

	<div class="product-view">
		<div class="product-top">
			<div class="product-images col-l-8 col-m-7 col-m-12 tight-left clearfix">
                @if(count($product->images) > 1)
                    <div class="thumbnails">
                        @foreach($product->images as $key => $image)
                            <a class="thumbnail" href="#" data-slide="{{$key+1}}" style="background-image: url('{{asset('images/products/' .$image->name)}}');"></a>
                        @endforeach
                    </div>
                @endif
				<div class="slider contain">
					<div class="container">
					@foreach($product->images as $key => $image)
						<div class="slide" style="background-image: url('{{asset('images/products/' .$image->name)}}');"></div>
					@endforeach
					</div>
				</div>
			</div>

			<div class="product-info">
                @if($product->manufacturer->image)
                    <a class="manufacturer text-center" href="#product-manufacturer-description" style="width: 100%; float: left; padding:1rem">
                        <img src="{{asset('images/manufacturers/' .$product->manufacturer->image->name)}}" alt="{{$product->manufacturer->name}} Logo">
                    </a>
                @endif

                <header class="header">
                    <h3 class="title end">
                        {{$product->name}}
                    </h3>
                    <h3 class="price end"><strong class="value">{{ceil($product->price * $product->vatgroup->amount)}}</strong>,-</h3>
                </header>

                <div class="summary">
                    <p class="lead muted content">
                        {!! $product->summary !!}
                    </p>
                </div>

                <form class="vertical" id="buy-form" action="{{route('cart.store')}}" method="POST">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="product_id" value="{{$product->id}}">
                    <div class="product-variants">
                        @foreach($product->variants as $variant)
                            <div class="variant" data-id="{{$variant->id}}">
                                <label for="variant-{{$variant->id}}">{{$variant->name}}</label>
                                <select name="variant-{{$variant->id}}">
                                    @foreach($variant->data['values'] as $value)
                                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <button class="huge primary buy" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
                    <button class="huge primary toggle-add-modal" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
                    <button class="huge primary toggle-add-modal-dummy" style="display: none;" data-toggle-modal="#add-modal" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
                </form>
			</div>
		</div>

		<hr>

		<div class="product-bottom col-xs-12 tight">
			<ul id="product-tabs" class="nav tabs clearfix">
				<li class="current"><a href="#product-info">@lang('store.product info')</a></li>
				<li><a href="#product-relations">@lang('store.related products')</a></li>
                <li><a href="#product-manufacturer-description">About {{$product->manufacturer->name}}</a></li>

                @foreach($product->producttabs as $tab)
                    <li><a href="#product-tab-{{$tab->id}}">{{$tab->title}}</a></li>
                @endforeach
			</ul>

            @foreach($product->producttabs as $tab)
                <div class="tab" id="product-tab-{{$tab->id}}">
                    {!! $tab->body !!}
                </div>
            @endforeach

			<div class="tab" id="product-info">
				{!! $product->description !!}
			</div>

			<div class="tab clearfix" id="product-relations">
				<div class="products-grid">
					@foreach($product->relations as $related)
                        @include('front.partial.products-block', ['product' => $related])
					@endforeach
				</div>
			</div>

            <div class="tab" id="product-manufacturer-description">
                {!! $product->manufacturer->description !!}
            </div>
		</div>
	</div>

    <div id="add-modal" class="modal center fade">
        <div class="modal-content">
            <div class="variants">
                @foreach($product->variants as $variant)
                    <div class="variant" data-id="{{$variant->id}}">
                        <label for="variant-{{$variant->id}}">{{$variant->name}}</label>
                        <select name="variant-{{$variant->id}}">
                            @foreach($variant->data['values'] as $value)
                                <option value="{{$value['id']}}">{{$value['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>

            <button class="large primary buy" data-toggle-modal="#add-modal" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
        </div>
    </div>
@stop

@section('aside')
	<div class="block store-menu visible-m-up">
		<ul id="store-menu" class="nav vertical fancy">
			<?php
			$c = '';
			if (Request::is($category->getRoot()->getPath())) {
				$c = 'current';
			}
			?>
			<li>
				<a class="{{{$c}}}" href="{{url('/store/' .$category->getRoot()->slug)}}">{{$category->getRoot()->name}}</a>
			</li>
			<?php
			echo $category->getRoot()->renderMenu('/store', 3);
			?>
		</ul>
	</div>
	@parent
@stop


@section('scripts')
	@parent

	<script type="text/javascript">
        var slider = $(".product-images .slider");
        var thumbnails = $(".product-images .thumbnails");

		slider.friluftSlider({
			delay: 4000,
			transitionSpeed: 400,
            slideLinks: false,
		});

        $(".product-images .thumbnails .thumbnail").click(function(e) {
            e.preventDefault();
            var id = $(this).attr('data-slide');
            slider.friluftSlider("goTo", id);
            slider.friluftSlider("stop");
            $(".product-images .thumbnails .thumbnail.active").removeClass('active');
            $(this).addClass('active');

            return false;
        });

        slider.on('slider.next', function() {
            var id = slider.data('friluftSlider').currentIndex;

            $('.product-images .thumbnails .thumbnail.active').removeClass('active');

            $('.product-images .thumbnails .thumbnail[data-slide="' + id + '"]').addClass('active');
        });

        // setup add modal
        var addModal = $("#add-modal");
        addModal.find('button.buy').click(function() {
            $("#buy-form").trigger('submit');
        });

        $("form .toggle-add-modal").click(function() {
            if ($('.product-info .product-variants').children().length > 0) {
                //has variants
                addModal.gsModal('show');
            }else {
                $("#buy-form").trigger('submit');
            }
        });

        //stick the buy button to bottom on mobile
        var callback = function() {
            console.log('checking');
            var btn = $(".product-top form .toggle-add-modal");
            var dummy = $(".product-top form .toggle-add-modal-dummy");

            if (btn.hasClass('fixed')) {
                if ( ! dummy.is(':in-viewport')) {
                    btn.addClass('fixed');
                    $("#footer").css('padding-bottom', (btn.outerHeight() + 24) + 'px');
                    dummy.css({
                        display: 'block',
                        visibility: 'hidden',
                    });
                }else {
                    btn.removeClass('fixed');
                    $("#footer").css('padding-bottom', '0');
                    dummy.css({
                        display: 'none',
                        visibility: 'auto',
                    });
                }
            }else {
                if ( ! btn.is(':in-viewport')) {
                    btn.addClass('fixed');
                    $("#footer").css('padding-bottom', (btn.outerHeight() + 24) + 'px');
                    dummy.css({
                        display: 'block',
                        visibility: 'hidden',
                    });
                }else {
                    btn.removeClass('fixed');
                    $("#footer").css('padding-bottom', '0');
                    dummy.css({
                        display: 'none',
                        visibility: 'auto',
                    });
                }
            }
        };
        callback();
        $(window).bind('scroll resize', _.throttle(function() {
            callback();
        }, 50));

        // setup buy event
        var form = $("#buy-form");
        var cart = $("#header .cart-container");
        form.on('submit', function(e) {
            e.preventDefault();
            $(this).serialize();

            // show buy modal
            var thumbnail = $(this).parents('.product-view').find('.product-images .slider .slide').first().css('background-image');
            thumbnail = thumbnail.replace('url(', '');
            thumbnail = thumbnail.replace(')', '');

            var title = $(this).parents('.product-view').find('.header .title .manufacturer .title').text();

            var price = $(this).parents('.product-view').find('.header .price .value').first().text();

            showBuyModal(1, thumbnail, title, price);

            //post form via ajax
            $.post($(this).attr('action'), $(this).serialize(), function(response) {
                var total = response.total;

                var left = 1000 - total;
                if (left > 0) {
                    $("#breadcrumbs .free-shipping-status").show().find('.value').text(left);
                }else {
                    $("#breadcrumbs .free-shipping-status").hide();
                }

                $.get(Friluft.URL + '/cart', function(html) {
                    cart.find('.cart-table').replaceWith(html);
                });
            });
        });
	</script>
@stop