@extends('master')

@section('scripts')
	<script>
		window.print();
	</script>
@stop

@section('body')
	<div id="packlist" class="ninesixty tower" style="max-width: 720px">
		<div class="row">
			<div class="col-xs-6">
				<img id="logo" src="{{asset('images/friluft.png')}}" alt="logo"/>
			</div>

			<div class="col-xs-6 text-right">
				123Concept AS<br>
				Johan Stangs Plass 2<br>
				1767 Halden
			</div>
		</div>

		<hr/>

		<div class="row">
			<div class="col-xs-6">
				<h5>Fakturaadresse</h5>
				<address>
					{{$order->billing_address['given_name']}} {{$order->billing_address['family_name']}}<br>
					{{$order->billing_address['street_address']}}, {{$order->billing_address['postal_code']}}
					{{$order->billing_address['city']}}, {{$order->billing_address['country']}}.
				</address>
			</div>

			<div class="col-xs-6">
				<h5>Leveringsadresse</h5>
				<address>
					{{$order->shipping_address['given_name']}} {{$order->shipping_address['family_name']}}<br>
					{{$order->shipping_address['street_address']}}, {{$order->shipping_address['postal_code']}}
					{{$order->shipping_address['city']}}, {{$order->shipping_address['country']}}.
				</address>
			</div>

			<hr class="small"/>

			<div class="col-xs-12">
				Kundenummer: {{$order->user->id}}<br>
				Ordrenummer: {{$order->id}}<br>
				E-Post: {{$order->user->email}}
			</div>
		</div>

		<hr/>

		<div class="row">
			<div class="col-xs-12">
				<table class="bordered">

					<thead>
						<tr>
							<th>Pakket</th>
							<th>Produkt</th>
							<th>Antall</th>
							<th>Art. Nr</th>
						</tr>
					</thead>

					<tbody>
					@foreach($items as $id => $item)
						<?php
						if ($item['type'] != 'shipping_fee') {
                            # get product ID and model
                            $productID = $item['reference']['id'];
                            $product = Friluft\Product::withTrashed()->find($productID);

                            if ($product == null) {
                                return "Invalid product data";
                            }

                            # get stock and name
                            $stock = $product->stock;
                            $name = $product->name;

                            $variantString = '';
                            if (count($product->variants) > 0) {
                                # get the variants we ordered
                                $variants = $item['reference']['options']['variants'];

                                # add to variantString
                                foreach($variants as $variantID => $value) {
                                    $variantModel = Friluft\Variant::find($variantID);
                                    $variantString .= $variantModel->name .': ' .$variantModel->getValueName($value) .', ';
                                }

                                # get stock and add 1 to it (we want to display the actual physical stock here)
                                $stock = $product->getStock($item['reference']['options']);
                                $stock++;
                            }
                            $variantString = rtrim($variantString, ', ');

                            if (strlen($variantString) > 0) $variantString = ' [' .$variantString .']';

                            # color the item by stock
                            $class = 'color-success';
                            if ($stock < $item['quantity']) $class = 'color-error';

                            $title = '<span class="' .$class .'">' .$name .$variantString .' (' .$stock .'/' .$item['quantity'] .' in stock)</span>';
						}
						?>
						<tr class="item" data-id="{{$id}}">
							<td>
								<div style="width: 24px; height: 24px; margin: 4px; border: 1px solid #333; border-radius: 2px;"></div>
							</td>
							<td>
								<strong>{{$product->manufacturer->name}}</strong>
								<span>{{$name .' [' .$variantString .'] (' .$stock .' ' .trans('store.in stock') .')'}}</span>
							</td>
							<td>
								{{$item['quantity']}}
							</td>
							<td>{{$product->articlenumber}}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@stop