@extends('front.layout')

@section('title')
	Cart - Store - @paren
@stop

@section('breadcrumbs')
	{!! Breadcrumbs::render('cart') !!}
@stop

@section('article')
	<table class="bordered">
		<tfoot>
			<tr>
				<td colspan="2">Total:</td>
				<td>{{$total}}</td>
			</tr>
		</tfoot>
		<tbody>
			@foreach($items as $item)
			<?php $model = $item['model']; ?>
				<tr id="cart-item-{{$item['id']}}">
					<td class="name">{{$model->name}}</td>
					<td class="quantity">
						<input type="number" name="quantity_{{$item['id']}}" value="{{$item['quantity']}}">
					</td>
					<td class="sub-total">
						<span class="price">{{$model->price * $item['quantity']}}</span>,-
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@stop