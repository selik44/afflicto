@extends('front.layout')


@section('title')
	Cart - Store - @parent
@stop


@section('breadcrumbs', Breadcrumbs::render('cart'))


@section('article')
	<h1 class="end">Cart</h1>
	<hr style="margin-top: 0.5rem">

	<div id="store-cart">
		<table class="cart-table bordered">
			<thead>
				<tr>
					<th>Product</th>
					<th>Options</th>
					<th>Quantity</th>
					<th>Subtotal</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">Total:</td>
					<td>{{$total}}</td>
				</tr>
			</tfoot>
			<tbody>
				@foreach($items as $item)
					<?php
					$model = $item['model'];
					?>
					<tr>
						<td class="product">
							<div class="col-xs-4">
								<a href="{{$item['url']}}"><img class="thumbnail" src="{{asset('images/products/' .$model['images'][0])}}"></a>
							</div>
							<div class="col-xs-6">
								<h4>{{$model['name']}}</h4>
							</div>
						</td>

						<td class="attributes">
							
						</td>

						<td class="quantity">
							<input type="number" name="quantity" value="{{$item['quantity']}}">
						</td>

						<td class="subtotal">
							<h6>{{$model['price'] * $item['quantity']}}</h6>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<hr>

		<a href="{{route('store.checkout')}}" class="button large primary">Checkout</a>
	</div>
@stop


@section('scripts')
	@parent

	<script type="text/javascript">
		
	</script>
@stop