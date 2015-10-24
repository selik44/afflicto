<?php
        $variants = '';
        foreach($product->variants as $variant) {
            $name = strtolower(str_replace(' ', '-', $variant->name));
            $values = implode(',', array_column($variant->data['values'], 'name'));
            $variants .= ' data-variant-' .$name .'="' .$values .'"';
        }

        $disabled = '';

        if ($product->variants->count() == 0) {
            if ($product->stock <= 0) {
                $disabled = 'disabled="disabled" ';
            }
        }

		# get availability
		$availability = $product->getAvailability();
		if ($availability == \Friluft\Product::AVAILABILITY_BAD) {
			$availabilityClass = 'availability-bad';
		}else if ($availability == \Friluft\Product::AVAILABILITY_WARNING) {
			$availabilityClass = 'availability-warning';
		}else {
			$availabilityClass = 'availability-good';
		}
?>

<div
        id="product-{{$product->id}}"
        class="product products-block {{$availabilityClass}}"
        data-id="{{$product->id}}"
        data-variants="{{count($product->variants)}}"
        data-stock="{{$product->stock}}"
        data-price="{{round($product->getDiscountPrice() * $product->vatgroup->amount)}}"
        data-manufacturer="{{($product->manufacturer) ? $product->manufacturer->id : ''}}"
    >
	<?php
	$img = $product->images()->first();
	if ($img == null) {
		$img = '';
	}else {
		$img = asset('images/products/' .$img->getThumbnail());
	}

	$link = url($product->getPath());
	?>

	<div class="preview">
		<a href="{{$link}}" style="background-image: url('{{$img}}');" class="image">
            <div class="overlay">
                <div class="tags">
                    @foreach($product->tags as $tag)
                        @if($tag->visible)
                        <span class="tag" style="background-color: {{$tag->color}};"><i class="{{$tag->icon}}"></i> {{$tag->label}}</span>
                        @endif
                    @endforeach

					@if($availability == \Friluft\Product::AVAILABILITY_WARNING) {
						<span class="tag tag-availability availability-warning"><i class="fa fa-question"></i>
							<?php
								/*
								$str = strtolower($product->getExpectedArrival()->diffForHumans(\Carbon\Carbon::now(), true));
								$str = str_replace('days', 'dager', $str);
								$str = str_replace('day', 'dag', $str);
								$str = str_replace('weeks', 'uker', $str);
								$str = str_replace('week', 'uke', $str);
								$str = str_replace('months', 'måneder', $str);
								$str = str_replace('month', 'måned', $str);
								echo 'Kommer om ' .$str;
								*/

								echo 'Kommer ' .trans('carbon.in') .' ' .\Friluft\Utils\LocalizedCarbon::diffForHumans($product->getExpectedArrival(), null, true);
							?>
						</span>
					@elseif($availability == \Friluft\Product::AVAILABILITY_BAD) {
						<!--<span class="tag tag-availability availability-bad"><i class="fa fa-warning"></i>Utsolgt</span>-->
					@endif
                </div>
            </div>
        </a>
	</div>

	<header class="header clearfix">
		<a href="{{$link}}" class="title">
            @if(isset($product->manufacturer))
                <h6 class="manufacturer end">{{$product->manufacturer->name}}</h6>
            @endif
			<h5 class="name end">{{$product->name}}</h5>
		</a>

		<h4 class="price end discount">
            <span class="value">{{numberFormat(round($product->getDiscountPrice() * $product->vatgroup->amount))}}</span>,-
            @if($product->hasDiscount())
                <br>
                <del class="value">{{numberFormat(round($product->price * $product->vatgroup->amount))}},-</del>
            @endif
        </h4>
	</header>

    @if(isset($withBuyButton) && $withBuyButton)
        <button {{$disabled}}class="primary buy" data-toggle-modal="#add-modal-{{$product->id}}">@lang('store.add to cart')</button>
    @endif
</div>

@if(isset($withBuyButton) && $withBuyButton)
    <div class="modal" id="add-modal-{{$product->id}}" style="max-width: 300px;">
        <div class="modal-content">
            @include('front.partial.product-buy-form', ['product' => $product, 'modal' => true])
        </div>
    </div>

    @section('scripts')
        @parent

        <script>
            (function($, window, document, undefined) {
                var block = $("#product-{{$product->id}}");
                var form = $("#add-modal-{{$product->id}} form");
                var modal = $("#add-modal-{{$product->id}}");
                var cart = $("#cart-table").parent();

                console.log(form);

                // setup submit event
                form.on('submit', function(e) {
                    //hide modal
                    modal.gsModal('hide');
                    console.log('Submitting buy now');
                    e.preventDefault();
                    klarnaSuspend();

                    // show buy modal
                    var thumbnail = block.find('.image').css('background-image');
                    thumbnail = thumbnail.replace('url(', '');
                    thumbnail = thumbnail.replace(')', '');
                    var title = block.find('.header .title .name').text();
                    var price = block.find('.header .price .value').first().text();

                    //post form via ajax
                    klarnaSuspend();
                    $.post($(this).attr('action'), $(this).serialize(), function(response) {
                        console.log(response);
                        showBuyModal(1, thumbnail, title, price);

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
                        var withShipping = "false";
                        if (cart.find('tr.shipping').length > 0) withShipping = "true";
                        $.get(Friluft.URL + '/api/cart', {withShipping: withShipping}, function(html) {
                            cart.find('.cart-table').replaceWith(html);
                        });
                    }).done(function() {
                        klarnaResume();
                    });
                });
            })(jQuery, window, document);
        </script>
    @endsection
@endif