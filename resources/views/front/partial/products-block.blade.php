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
?>

<div
        id="product-{{$product->id}}"
        class="product products-block"
        data-id="{{$product->id}}"
        data-variants="{{count($product->variants)}}"
        data-stock="{{$product->stock}}"
        data-price="{{ceil($product->price * $product->vatgroup->amount)}}"
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

		<h3 class="price end"><span class="value">{{ceil($product->price * $product->vatgroup->amount)}}</span>,-</h3>
	</header>

    @if(isset($withBuyButton) && $withBuyButton)
        <button class="primary buy" data-toggle-modal="#add-modal-{{$product->id}}">@lang('store.add to cart')</button>
    @endif
</div>

@if(isset($withBuyButton) && $withBuyButton)
    <div class="modal" id="add-modal-{{$product->id}}">
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
                var modal = block.find('.modal');
                var cart = $("#cart-table").parent();

                console.log(form);

                // setup submit event
                form.on('submit', function(e) {
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