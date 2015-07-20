<div class="cart-table">
	@if(count($items) == 0)
		<p class="lead text-center empty-message">@lang('store.cart empty')</p>
	@else
		<table class="bordered">
            <thead>
            <tr>
                <th colspan="2">@lang('store.product')</th>
                <th>@lang('store.quantity')</th>
            </tr>
            </thead>
			<tfoot>
				<tr>
                    <td></td>
                    <td></td>
                    <td></td>
					<td>
                        <h6 class="end total">@lang('store.total'): <span class="value">{{$total}}</span>,-</h6>
					</td>
				</tr>
			</tfoot>
			<tbody>
			@foreach($items as $item)
				<?php
				$model = $item['model'];
				?>
				<tr class="item" data-id="{{$item['id']}}" data-price="{{$model->price * $model->vatgroup->amount}}">
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
							<button title="Remove" class="error remove appended"><i class="fa fa-trash"></i></button>
						</div>
					</td>

					<td class="subtotal"  style="width: 1%;">
						<h4 class="end"><span class="value">{{round($model->price * $model->vatgroup->amount) * $item['quantity']}}</span>,-</h4>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
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

        function setTotal(total) {
            container.find('.cart-table table tfoot .total .value').html(total);
        };

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

			$.post(Friluft.URL + '/cart/' + id + '/quantity', payload, function(response) {
				console.log('Changed quantity, response:');
				console.log(response);
				self.removeAttr('disabled').removeClass('disabled');

				//update price
				var price = parseFloat(item.attr('data-price'));
				var subTotal = Math.round(price * quantity);

				item.find('.subtotal .value').html(subTotal);

                setTotal(response.total);

				klarnaResume();
			});
		});

		//-------- remove item -----------//
		container.on('click', '.quantity button.remove', function() {
			$(this).addClass('disabled').attr('disabled', 'disabled');

			var item = $(this).parents('.item').first();
			var id = item.attr('data-id');

			klarnaSuspend();

			$.post(Friluft.URL + '/cart/' + id, {_method: 'DELETE', _token: Friluft.token}, function(response) {
				console.log('Removed, response:');
				console.log(response);

				item.slideUp(function() {
					$(this).remove();

					//is the cart empty? If so, redirect to home
					if (container.find('.cart .item').length <= 0) {
						//window.location.href = Friluft.URL;
					}
				});

                setTotal(response.total);

				klarnaResume();
			});
		});
	</script>
@stop