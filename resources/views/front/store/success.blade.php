@extends('front.layout')

@section('title')
    Success - Checkout - @parent
@stop

@section('article')
    <div class="paper" style="padding: 2rem;">
        <h2>@lang('store.done')</h2>
        {!! $snippet !!}
    </div>
@stop

@section('scripts')
	@parent

	<script type="text/javascript">
		console.log('y');
		ga('require', 'ecommerce');

		ga('ecommerce:addTransaction', {
			id: '{{$id}}',
			affiliation: '{{\Friluft\Store::current()->name}}',
			revenue: '{{$revenue}}',
			shipping: '{{$shipping}}',
			tax: '{{$tax}}',
			currency: 'NOK',
		});

		@foreach($items as $item)
			<?php
				$model = $item['model'];
			?>

				ga('ecommerce:addItem', {
					id: '{{$id}}',
					name: '{{$model->name}}',
					sku: '{{$item['product_id']}}',
					category: '',
					price: '{{$model->getDiscountPrice() * $model->vatgroup->amount}}',
					quantity: '{{$item['quantity']}}',
					currency: 'NOK',
				});
		@endforeach

		ga('ecommerce:send');
	</script>
@stop