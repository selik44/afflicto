@extends('admin.layout')

@section('title')
	@lang('admin.receivals') - @parent
@stop

@section('header')
	<h3 class="title">@lang('admin.receivals')</h3>
@stop

@section('content')
	{!! Former::open()
		->method('PUT')
		->action(route('admin.receivals.receive.store', $receival))
	 !!}

	<h4>@lang('admin.products')</h4>
	<table class="bordered">
		<tbody>
		@foreach($receival->getProductsWithModels() as $item)
			<?php
				$product = $item['model'];
			?>
			<tr class="product">
				<td>{{$product->name}}</td>
				<td>
					@if($product->hasVariants())
						<table>
							<?php

							$stock = ($product->variants_stock) ? $product->variants_stock : [];

							$rootVariant = $product->variants[0];
							if (count($product->variants) > 1) {
								foreach($rootVariant->data['values'] as $rootValue) {
									foreach($product->variants as $variant) {
										if ($rootVariant == $variant) continue;

										foreach($variant['data']['values'] as $value) {
											$stockID = $rootValue['id'] .'_' .$value['id'];
											$quantity = $item['order'][$stockID];

											$id = $product->id .'_' .$stockID;

											echo '<tr class="variant-' .$stockID .'" data-stock-id="' .$stockID .'">';
											echo '<td class="name">' .$rootValue['name'] .' ' .$value['name'] .'</td>';
											echo '<td class="quantity">' .$quantity .'</td>';
											echo '<td class="received"><input type="number" name="' .$id .'" value="0"></td>';
											echo '<tr>';
										}
									}
								}
							}else {
								echo '<tr>
									<th>Variant</th>
									<th>Antall</th>
									<th>Mottatt</th>
								</tr>';

								foreach($rootVariant->data['values'] as $value) {
									$stockID = $value['id'];
									$quantity = $item['order'][$stockID];

									$id = $product->id .'_' .$stockID;

									echo '<tr class="variant variant-' .$value['id'] .'" data-stock-id="' .$stockID .'">';
									echo '<td class="name">' .$value['name'] .'</td>';
									echo '<td class="quantity">' .$quantity .'</td>';
									echo '<td class="received"><input type="number" name="' .$id .'" value="0"></td>';
									echo '</tr>';
								}
							}
							?>
						</table>
					@else
						<?php
							$quantity = $item['order'];
							$id = $product->id;
						?>
						<table>
							<tr>
								<th>Antall</th>
								<th>Mottatt</th>
							</tr>

							<tr>
								<td class="quantity">{{$quantity}}</td>
								<td class="received">
									<input type="number" name="{{$id}}" value="0">
								</td>
							</tr>
						</table>
					@endif
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>

	{!! Former::submit('Registrer Mottak')->class('large success') !!}

	{!! Former::close() !!}
@stop

@section('footer')

@stop