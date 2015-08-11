@extends('front.layout')


@section('title')
	{{{$product->name}}} - {{{$product->categories->first()->name}}} - @parent
@stop


@section('breadcrumbs', Breadcrumbs::render('product', $product, $category))

@section('article')

	<div class="product-view" data-id="{{$product->id}}" data-variants="{{count($product->variants)}}" data-stock="{{$product->stock}}">
		<div class="product-top">
			<div class="product-images col-l-8 col-m-7 col-m-12 tight-left clearfix">
                @if(count($product->images) > 1)
                    <div class="thumbnails">
                        <?php $active = 'active'; ?>
                        @foreach($product->images as $key => $image)
                            <a class="thumbnail {{$active}}" href="#" data-slide="{{$key+1}}" style="background-image: url('{{asset('images/products/' .$image->getThumbnail())}}');"></a>
                            <?php $active = '';?>
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

			<div class="product-info paper">
                @if($product->manufacturer->image)
                    <a class="manufacturer text-center" href="#product-manufacturer-description" style="width: 100%; float: left; padding:1rem">
                        <img src="{{asset('images/manufacturers/' .$product->manufacturer->image->name)}}" alt="{{$product->manufacturer->name}} Logo">
                    </a>
                @else
                    <a class="manufacturer text-center" href="#product-manufacturer-description" style="width: 100%; float: left; padding:1rem">
                        {{$product->manufacturer->name}}
                    </a>
                @endif

                <header class="header">
                    <h3 class="title end">
                        {{$product->name}}
                    </h3>
                    <h3 class="price end"><strong class="value">{{ceil($product->price * $product->vatgroup->amount)}}</strong>,-</h3>
                </header>

                <form class="vertical" id="buy-form-{{$product->id}}" action="{{route('api.cart.store')}}" method="POST">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="product_id" value="{{$product->id}}">

                    @if(count($product->variants) > 0)
                            @if(count($product->variants) == 1)
                                <div class="product-variants">
                                    @foreach($product->variants as $variant)
                                        <div class="variant" data-id="{{$variant->id}}">
                                            <label for="variant-{{$variant->id}}">{{$variant->name}}</label>
                                            <select name="variant-{{$variant->id}}">
                                                @foreach($variant->data['values'] as $value)
                                                    @if($product->variants_stock[$value['id']] <= 0)
                                                        <option disabled="disabled" value="{{$value['id']}}">
                                                            {{$value['name']}}
                                                            @if ($product->manufacturer)
                                                            @endif
                                                        </option>
                                                    @else
                                                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            @else
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
                            @endif
                    @endif

                    <div class="product-stock">
                        <p class="true lead color-success">
                            <i class="fa fa-check"></i> @lang('store.in stock')
                        </p>

                        <p class="false lead color-warning">
                            <i class="fa fa-exclamation-triangle"></i> @lang('store.out of stock')
                        </p>
                    </div>

                    <?php
                        $disabled = '';
                        if (count($product->variants) > 0) {

                        }else if ( ! $product->manufacturer->always_allow_orders) $disabled = 'disabled="disabled" ';
                    ?>
                    <button {{$disabled}}class="huge primary buy" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
                    <button {{$disabled}}class="huge primary toggle-add-modal" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
                    <button {{$disabled}}class="huge primary toggle-add-modal-dummy" style="display: none;" data-toggle-modal="#add-modal" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
                </form>

                <div class="summary">
                    {!! $product->summary !!}
                </div>
			</div>
		</div>

		<div class="paper product-bottom col-xs-12 tight">
			<ul id="product-tabs" class="nav tabs clearfix">
                @if(mb_strlen(trim(strip_tags($product->manufacturer->description))) > 3)
                    <li><a href="#product-manufacturer-description">@lang('store.about') {{$product->manufacturer->name}}</a></li>
                @endif

                @if(mb_strlen($product->description) > 3)
                    <li><a href="#product-info">@lang('store.product info')</a></li>
                @endif

                @foreach($product->producttabs as $tab)
                    <li><a href="#product-tab-{{$tab->id}}">{{$tab->title}}</a></li>
                @endforeach

                @if($product->relations->count() > 0)
                    <li><a href="#product-relations">@lang('store.related products')</a></li>
                @endif
			</ul>

            @if(mb_strlen(trim(strip_tags($product->manufacturer->description))) > 3)
            <div class="tab" id="product-manufacturer-description">
                {!! $product->manufacturer->description !!}
            </div>
            @endif

            @if(mb_strlen($product->description) > 3)
            <div class="tab" id="product-info">
                {!! $product->description !!}
            </div>
            @endif

            @foreach($product->producttabs as $tab)
                <div class="tab" id="product-tab-{{$tab->id}}">
                    {!! $tab->body !!}
                </div>
            @endforeach

            @if($product->relations->count() > 0)
                <div class="tab clearfix" id="product-relations">
                    <div class="products-grid">
                        @foreach($product->relations as $related)
                            @include('front.partial.products-block', ['product' => $related, 'withBuyButton' => true])
                        @endforeach
                    </div>
                </div>
            @endif
		</div>
	</div>

    <div id="add-modal-{{$product->id}}" class="modal center fade">
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

            <button class="large primary buy" data-toggle-modal="#add-modal-{{$product->id}}" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
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
				<a class="{{{$c}}}" href="{{url($category->getRoot()->slug)}}">{{$category->getRoot()->name}}</a>
			</li>
			<?php
			echo $category->getRoot()->renderMenu('', 3);
			?>
		</ul>
	</div>
	@parent
@stop

@section('scripts')
	@parent

	<script type="text/javascript">
        (function($, window, document, undefined) {
            var slider = $(".product-view .product-images .slider");
            var thumbnails = $(".product-view .product-images .thumbnails");

            var alwaysAllowOrders = <?= ($product->manufacturer->always_allow_orders) ? "true" : "false" ?>;

            //set active product tab
            var tab = $("#product-tabs li").first();
            tab = $(tab.find('a').attr('href'));
            $("#product-tabs").gsTabs("show", tab);

            slider.friluftSlider({
                delay: 4000,
                transitionSpeed: 400,
                slideLinks: false,
            });


            $(".product-view .product-top .product-images .thumbnails .thumbnail").click(function(e) {
                e.preventDefault();
                var id = $(this).attr('data-slide');
                slider.friluftSlider("goTo", id);
                slider.friluftSlider("stop");
                $(".product-view .product-top .product-images .thumbnails .thumbnail.active").removeClass('active');
                $(this).addClass('active');

                return false;
            });


            slider.on('slider.next', function() {
                var id = slider.data('friluftSlider').currentIndex;

                $('.product-view .product-top .product-images .thumbnails .thumbnail.active').removeClass('active');

                $('.product-images .thumbnails .thumbnail[data-slide="' + id + '"]').addClass('active');
            });

            // setup stock status text
            if (parseInt($(".product-view").attr('data-variants')) > 0) {
                var stock = JSON.parse('{!! json_encode($product->variants_stock) !!}');
                console.log('stock is:');
                console.log(stock);
                var $stock = $(".product-view .product-top .product-stock");

                function updateStock() {
                    console.log('updating stock status');
                    //get the current stock ID
                    var stockID = []
                    $(".product-view .product-top form .product-variants .variant").each(function() {
                        var select = $(this).find('select');
                        stockID.push(select.val());
                    });

                    stockID = stockID.join('_');
                    var stockValue = parseInt(stock[stockID]);

                    if (stockValue > 0) {
                        $(".product-view form button.buy, button.toggle-add-modal, button.toggle-add-modal-dummy").removeAttr('disabled');
                        $(".product-view form .product-stock .true").show().siblings('.false').hide();
                        //$("form .product-stock .true .quantity").text(stockValue);
                    }else {
                        $(".product-view form button.buy, button.toggle-add-modal, button.toggle-add-modal-dummy").attr('disabled', 'disabled');
                        $(".product-view form .product-stock .true").hide().siblings('.false').show();
                    }
                }

                updateStock();

                //listen for change event on the variant form fields
                $(".product-view form .product-variants .variant select").change(function() {
                    updateStock();
                });
            }else {
                var stockNumber = parseInt($(".product-view").attr('data-stock'));
                if (stockNumber > 0) {
                    $(".product-view form .product-stock .true").show().siblings('.false').hide();
                    $(".product-view form button.buy, button.toggle-add-modal, button.toggle-add-modal-dummy").removeAttr('disabled');
                }else {
                    $(".product-view form .product-stock .true").hide().siblings('.false').show();
                    $(".product-view form button.buy, button.toggle-add-modal, button.toggle-add-modal-dummy").attr('disabled', 'disabled');
                }
            }


            // setup add modal
            var addModal = $("#add-modal-{{$product->id}}");
            addModal.find('button.buy').click(function() {
                console.log('#add-modal-{{$product->id}} button.buy CLICKED!');
                $('#buy-form-{{$product->id}}').trigger('submit');
            });

            $(".product-view .product-top form .toggle-add-modal").click(function(e) {
                e.preventDefault();
                if ($('.product-view .product-info .product-variants').children().length > 0) {
                    //has variants
                    addModal.gsModal('show');
                }else {
                    $("#buy-form-{{$product->id}}").trigger('submit');
                }
            });

            //stick the buy button to bottom on mobile
            var callback = function() {
                var btn = $(".product-view .product-top form .toggle-add-modal");
                var dummy = $(".product-view .product-top form .toggle-add-modal-dummy");

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
            var form = $("#buy-form-{{$product->id}}");

            var cart = $("#header .cart-container");
            form.on('submit', function(e) {
                e.preventDefault();

                // show buy modal
                var thumbnail = $(this).parents('.product-view').find('.product-images .slider .slide').first().css('background-image');
                thumbnail = thumbnail.replace('url(', '');
                thumbnail = thumbnail.replace(')', '');

                var title = $(this).parents('.product-view').find('.header .title .manufacturer .title').text();

                var price = $(this).parents('.product-view').find('.header .price .value').first().text();

                showBuyModal(1, thumbnail, title, price);

                //post form via ajax
                $.post($(this).attr('action'), $(this).serialize(), function(response) {
                    var total = Math.round(parseFloat(response.total));

                    //update free shipping status
                    var left = 800 - total;
                    if (left > 0) {
                        $("#breadcrumbs .free-shipping-status").show().find('.value').text(left);
                    }else {
                        $("#breadcrumbs .free-shipping-status").hide();
                    }

                    //update cart-toggle status
                    var quantity = response.quantity;
                    $("#header .cart-toggle .quantity").text(response.quantity);
                    $("#header .cart-toggle .total").text(Math.round(parseFloat(response.total)));

                    //update cart table
                    $.get(Friluft.URL + '/api/cart', function(html) {
                        cart.find('.cart-table').replaceWith(html);
                    });
                });
            });
        })($, window, document);
	</script>
@stop