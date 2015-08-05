<div class="cart-table">
	@if(count($items) == 0)
		<p class="lead text-center empty-message">@lang('store.cart empty')</p>
	@else
		<table class="bordered">
			<tbody>
			@foreach($items as $item)
				<?php
				$model = $item['model'];
				?>
				<tr class="item" data-id="{{$item['id']}}" data-price="{{round($model->price * $model->vatgroup->amount)}}">
					<td class="image" style="width: 80px;">
						<a href="{{$item['url']}}"><img class="thumbnail" src="{{asset('images/products/' .$item['model']->images()->first()->name)}}"></a>
					</td>

					<td class="product">
						<h5 class="end"><a href="{{$item['url']}}">{{$model['name']}}</a></h5>
						<ul class="variants">
							@foreach($model->variants as $variant)
								<li class="variant" data-id="{{$variant->id}}">
									<strong>{{$variant->name}}</strong>: <span>{{$item['options']['variants'][$variant->id]}}</span>
								</li>
							@endforeach
						</ul>
					</td>

					<td class="quantity" style="width: 1%;">
						<div class="input-append">
							<input type="number" min="1" name="quantity" value="{{$item['quantity']}}">
							<button title="Remove" class="error remove appended"><i class="fa fa-close"></i></button>
						</div>
					</td>

					<td class="subtotal"  style="width: 1%;">
						<h4 class="end"><span class="value">{{round($model->price * $model->vatgroup->amount * $item['quantity'])}}</span>,-</h4>
					</td>
				</tr>
			@endforeach

            @if(isset($withShipping) && $withShipping)
                <tr class="shipping" data-price="{{$shipping['unit_price'] / 100}}">
                    <td class="icon">
                        <i class="fa fa-truck"></i>
                    </td>
                    <td>
                        <h3 class="end">@lang('store.shipping.shipping')</h3>
                        <p class="lead">@lang('store.shipping.' .$shipping['name'])</p>
                    </td>
                    <td colspan="2" class="value"><h4>{{$shipping['unit_price'] / 100}},-</h4></td>
                </tr>
            @endif
			</tbody>
		</table>

        <div class="footer clearfix">
            <div class="total">
                <?php
                    $t = $total;

                    if (isset($withShipping) && $withShipping) $t += $shipping['unit_price'] / 100;
                ?>
                <h3>@lang('store.total'): <span class="value">{{round($t)}}</span>,-</h3>
            </div>
            @if(isset($withCheckoutButton) && $withCheckoutButton)
                <a class="button primary large" href="{{route('store.checkout')}}">@lang('store.to checkout') <i class="fa fa-chevron-right"></i></a>
            @endif
        </div>
	@endif
</div>

@section('scripts')
@parent
	<script>
		var cart = $(".cart-table");
        var container = cart.parent();

		function klarnaSuspend() {
			if (typeof window._klarnaCheckout !== 'undefined') {
				console.log('suspending...');
				window._klarnaCheckout(function (api) {
					api.suspend();
				});
			}
		}

		function klarnaResume() {
			if (typeof window._klarnaCheckout !== 'undefined') {
				console.log('resuming...');
				window._klarnaCheckout(function (api) {
					api.resume();
				});
			}
		}

		//-------- change quantity -----------//
		container.on('change', '.quantity input', function() {
			if ($(this).attr('disabled')) return;
			$(this).attr('disabled', 'disabled').addClass('disabled');

			var self = $(this);

			var item = $(this).parents('.item').first();
			var id = item.attr('data-id');
			var quantity = parseInt($(this).val());

			var payload = {
				_method: 'PUT',
				_token: Friluft.token,
				quantity: quantity,
			};

			klarnaSuspend();

			$.post(Friluft.URL + '/api/cart/' + id + '/quantity', payload, function(response) {
				console.log('Changed quantity, response:');
				console.log(response);
				self.removeAttr('disabled').removeClass('disabled');

                //update total on cart-toggle
                $("#header .cart-toggle .total").html(response.total);
                $("#header .cart-toggle .quantity").html(response.quantity);

				//update price
				var price = parseFloat(item.attr('data-price'));
				var subTotal = Math.round(price * quantity);

				item.find('.subtotal .value').html(subTotal);

                $.get(Friluft.URL + '/api/cart', {withShipping: "false"}, function(html) {
                    container.find('.cart-table').replaceWith(html);
                });

				klarnaResume();
			});
		});

		//-------- remove item -----------//
		container.on('click', '.quantity button.remove', function() {
			$(this).addClass('disabled').attr('disabled', 'disabled');

			var item = $(this).parents('.item').first();
			var id = item.attr('data-id');

			klarnaSuspend();

			$.post(Friluft.URL + '/api/cart/' + id, {_method: 'DELETE', _token: Friluft.token}, function(response) {
				console.log('Removed, response:');
				console.log(response);

				item.slideUp(function() {
					$(this).remove();
				});

                //update cart-toggle
                $("#header .cart-toggle .total").html(response.total);
                $("#header .cart-toggle .quantity").html(response.quantity);

                $.get(Friluft.URL + '/api/cart', {withShipping: "false"}, function(html) {
                    container.find('.cart-table').replaceWith(html);
                });

				klarnaResume();
			});
		});
	</script>
@stop