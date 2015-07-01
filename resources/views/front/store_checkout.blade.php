@extends('front.layout')


@section('title')
	Cart - Store - @parent
@stop


@section('breadcrumbs', Breadcrumbs::render('cart'))


@section('article')
	<div id="checkout">
		<div class="module">
			<div class="module-header"><h4>Cart</h4></div>
			<div class="module-content">
				@include('front.partial.cart-table')
			</div>
		</div>

		<br>
		<br>

		<div class="module">
			<div class="module-header"><h4>Checkout</h4></div>
			<div class="module-content">
				{!! $snippet !!}
			</div>
		</div>
	</div>
@stop


@section('scripts')
	@parent

	<script type="text/javascript">
		$(document).ready(function() {
			$("#klarna-checkout-container").css('overflow-x', 'visible');
		});
	</script>
@stop