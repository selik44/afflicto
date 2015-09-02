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

@section('ga')
	_gaq.push(['_set', 'currencyCode', 'NOK']);
	_gaq.push(['ecommerce:addTransaction', [
		'{{$id}}',
		'{{\Friluft\Store::current()->name}}',
		'{{$revenue}}',
		'{{$shipping}}',
		'{{$tax}}',
	]);

	@foreach($items as $item)
		<?php $model = $item['model']; ?>

		_gaq.push(['ecommerce:addItem',
			'{{$id}}',
			'{{$item['model']->name}}',
			'{{$item['product_id']}}',
			'',
			'{{$model->getDiscountPrice() * $model->vatgroup->amount}}',
			'{{$item['quantity']}}',
		]);
	@endforeach
@stop