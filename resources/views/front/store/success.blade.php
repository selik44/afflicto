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

	<script>
		ga('ecommerce:addTransaction', {
			id: '{{$id}}',
			affiliation: '{{\Friluft\Store::current()->name}}',
			revenue: '{{$revenue}}',
			shipping: '{{$shipping}}',
			tax: '{{$tax}}',
		});

		@foreach($items as $item)
			<?php $model = $item['model']; ?>
			ga('ecommerce:addItem', {
				id: '{{$id}}',
				name: '{{$item['model']->name}}',
				SKU: '{{$item['product_id']}}',
				category: '',
				price: '{{$model->getDiscountPrice() * $model->vatgroup->amount}}',
				quantity: '{{$item['quantity']}}',
			});
		@endforeach
	</script>
@stop