@extends('admin.layout')

@section('title')
	Orders - @parent
@stop

@section('page')
	<h2>Order #{{$order->id}}</h2>
	<div id="orders-edit">

		<div id="items" class="module">
			<div class="module-content">
				<table class="bordered">
						<tr>
							<th>Product</th>
							<th>Quantity</th>
							<th>Sub-total</th>
						</tr>
					</thead>

					<tbody>
						@foreach($items as $id => $item)
							<?php
								if ($item['type'] != 'shipping_fee') {
									# get product ID and model
									$productID = $item['reference']['id'];
									$product = Friluft\Product::find($productID);

									# get stock and name
									$stock = $product->stock;
									$name = $product->name;

									if (count($product->variants) > 0) {
										# get the variant we ordered
										$variants = $item['reference']['options']['variants'];

										# get the first variant value and ID
										$variant = array_values($variants)[0];
										$variantID = array_search($variant, $variants);

										# get the variant model
										$variantModel = Friluft\Variant::find($variantID);

										# got it?
										if ($variantModel) {
											# set stock and name
											$stock = $variantModel->data['values'][$variant]['stock'];
											$name = $name .' [' .$variant .']';
										}
									}

									# color the item by stock
									$class = 'success';
									if ($stock < $item['quantity']) $class = 'error';

									$title = $name .' (' .$stock .'/' .$item['quantity'] .' in stock)';
								}
							?>
							<tr class="item {{$class}}" data-id="{{$id}}">
								<td>

									{{$title}} &nbsp;&nbsp;<a target="_blank" href="{{url($product->getPath())}}"><i style="color: white;" class="fa fa-link color-white"></i></a>
								</td>
								<td>
									{{$item['quantity']}}
								</td>
								<td>kr {{$item['unit_price'] / 100}},-</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

	</div>
@stop