<div id="cart-manager">
	@if(count($items) == 0)
		<p class="lead text-center">Your cart is empty!</p>
	@else
		<table class="cart-table bordered">
			<tfoot>
				<tr>
					<td colspan="2">
						<div class="pull-right">
							<strong>Total:</strong> <span class="total">kr {{$total}},-</span>
						</div>
					<td>
				</tr>
			</tfoot>

			<tbody>
				@foreach($items as $item)
					<?php $model = $item['model']; ?>

					<tr id="cart-item-{{$item['id']}}">
						<td class="product">
							<div class="pull-left image">
								<img class="thumbnail" src="{{asset('images/products/' .$model['images'][0])}}">
							</div>

							<div class="pull-left info">
								<h4 class="name end">
									<a href="{{$item['url']}}">{{$model['name']}}</a>
									@if($item['quantity'] > 1)
									({{$item['quantity']}})
									@endif
								</h4>
							</div>
						</td>

						<td class="sub-total">
							kr <span class="price">{{$model['price'] * $item['quantity']}}</span>,-
						</td>
					</tr>

				@endforeach
			</tbody>
		</table>

		<div class="actions">
			<a href="{{route('store.cart')}}" class="large primary button">Full View</a>
		</div>
	@endif
</div>


@section('scripts')	
@parent
	<script>
		var cart = $("#cart-manager");
	</script>
@stop