@extends('admin.layout')

@section('title')
	@lang('admin.receivals') - @parent
@stop

@section('header')
	<h3 class="title">@lang('admin.receivals')</h3>
@stop

@section('content')
	{!! Former::open()
		->method('POST')
		->action(route('admin.receivals.receive.store'))
	 !!}

	<h4>@lang('admin.products')</h4>
	<table>
		<thead>

		</thead>
		<tbody>
		@foreach($receival->getProductsWithModels() as $product)
			<?php
				$model = $product['model'];
			?>
			<tr class="product">
				<td>{{$model->name}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>

	{!! Former::submit('Save')->class('large success') !!}

	{!! Former::close() !!}
@stop

@section('footer')

@stop