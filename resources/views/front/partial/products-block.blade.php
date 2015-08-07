<?php
        $variants = '';
        foreach($product->variants as $variant) {
            $name = strtolower(str_replace(' ', '-', $variant->name));
            $values = implode(',', array_column($variant->data['values'], 'name'));
            $variants .= ' data-variant-' .$name .'="' .$values .'"';
        }

        $disabled = '';

        if ($product->variants->count() == 0) {
            if ($product->stock <= 0) $disabled = 'disabled="disabled" ';
        }
?>

<div id="product-{{$product->id}}" class="product products-block" {!! $variants !!} data-id="{{$product->id}}" data-price="{{ceil($product->price * $product->vatgroup->amount)}}" data-manufacturer="{{($product->manufacturer) ? $product->manufacturer->id : ''}}">
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
                        @unless($tag->type !== null)
                        <span class="tag" style="background-color: {{$tag->color}};"><i class="{{$tag->icon}}"></i> {{$tag->label}}</span>
                        @endunless
                    @endforeach
                </div>
            </div>
            @if(isset($withBuyButton) && $withBuyButton)
            <div class="actions visible-l-up">
                <button {{$disabled}}class="primary buy" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
            </div>
            @endif
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
    <button {{$disabled}}class="primary toggle-add-modal hidden-l-up" data-toggle-modal="#add-modal-{{$product->id}}" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
    @endif

	<footer class="footer">
		<hr class="divider shadow">
		<a class="buy" href="{{$link}}"><i class="fa fa-cart-plus"></i></a>
		<a class="share" href="#"><i class="fa fa-share-alt"></i></a>
	</footer>
</div>

@if(isset($withBuyButton) && $withBuyButton)
<div id="add-modal-{{$product->id}}" class="modal center fade">
    <div class="modal-content">
        <form id="buy-form-{{$product->id}}" class="vertical" action="{{route('api.cart.store')}}" method="POST">
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
            <button class="large primary buy" data-toggle-modal="#add-modal-{{$product->id}}" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
        </form>
    </div>
</div>


@section('scripts')
    @parent

    <script>
        (function($, window, document, undefined) {
            var block = $("#product-{{$product->id}}");
            var form = $("#buy-form-{{$product->id}}");
            var cart = $("#cart-table").parent();
            var addModal = $("#add-modal-{{$product->id}}");

            //buy on desktop
            block.find('.actions .buy').click(function(e) {
                console.log(form);
                e.preventDefault();
                if (form.find('.product-variants').children().length > 0) {
                    console.log('has variants, showing addModal');
                    //has variants
                    addModal.gsModal('show');
                }else {
                    console.log('has no variants, just submitting');
                    //just submit the form
                    form.trigger('submit');
                }
            });

            //submit form when we click buy inside the add-modal
            addModal.find('button.buy').click(function() {
                form.trigger('submit');
            });

            // setup buy event
            form.on('submit', function(e) {
                klarnaSuspend();
                console.log('Submitting buy now');
                e.preventDefault();
                $(this).serialize();

                // show buy modal
                var thumbnail = block.find('.image').css('background-image');
                thumbnail = thumbnail.replace('url(', '');
                thumbnail = thumbnail.replace(')', '');

                var title = block.find('.header .title .name').text();
                var price = block.find('.header .price .value').first().text();

                //post form via ajax
                klarnaSuspend();
                $.post($(this).attr('action'), $(this).serialize(), function(response) {
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