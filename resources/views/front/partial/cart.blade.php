<div id="cart-manager">
	@if(count($items) == 0)
		<p class="lead text-center">Your cart is empty!</p>
	@else
		<table class="cart-table bordered">
			<thead>
			<tr>
				<th></th>
				<th>Quantity</th>
				<th>Subtotal</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th colspan="3"><h4 class="end">Total:</h4></th>
				<th><h4 class="end">{{$total}},-</h4></th>
			</tr>
			</tfoot>
			<tbody>
			@foreach($items as $item)
				<?php
				$model = $item['model'];
				?>
				<tr class="item" data-id="{{$item['id']}}" data-price="{{$model->price * $model->vatgroup->amount}}">
					<td>
						<div class="col-xs-4">
							<a href="{{$item['url']}}"><img class="thumbnail" src="{{asset('images/products/' .$item['model']->images()->first()->name)}}"></a>
						</div>
						<div class="col-xs-6">
							<h5>{{$model['name']}}</h5>

							<ul class="variants">
								@foreach($model->variants as $variant)
									<li class="variant" data-id="{{$variant->id}}">
										<strong>{{$variant->name}}</strong>: <span>{{$item['options']['variants'][$variant->id]}}</span>
									</li>
								@endforeach
							</ul>
						</div>
					</td>

					<td class="quantity">
						<div class="input-append">
							<input type="number" min="1" name="quantity" style="width: 60px;" value="{{$item['quantity']}}">
							<button title="Remove" class="error remove appended"><i class="fa fa-trash"></i></button>
						</div>
					</td>

					<td class="subtotal">
						<h6 class="end"><span class="value">{{($model->price * $model->vatgroup->amount) * $item['quantity']}}</span>,-</h6>
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
		var cart = $("#cart-manager");

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
		cart.find('.quantity input').change(function() {
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
				var subTotal = price * quantity;

				item.find('.subtotal .value').html(subTotal);

				klarnaResume();
			});
		});


		//-------- remove item -----------//
		cart.find('.quantity button.remove').click(function() {
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
					if (cart.find('.item').length <= 0) {
						window.location.href = Friluft.URL;
					}
				});

				klarnaResume();
			});
		});
	</script>
@stop