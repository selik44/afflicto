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
					{{$order->billing_address['given_name']}}<br>
					{{$order->billing_address['street_address']}}, {{$order->billing_address['postal_code']}}
					{{$order->billing_address['city']}}, {{$order->billing_address['country']}}.
				</address>
			</div>

			<div class="col-xs-6">
				<h5>Leveringsadresse</h5>
				<address>
					{{$order->shipping_address['given_name']}}<br>
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
							$product = Friluft\Product::find($productID);

							# get stock and name
							$stock = $product->stock;
							$name = $product->name;
							$manufacturer = ($product->manufacturer) ? $product->manufacturer->name : '';

                            $variantString = '';
                            if (count($product->variants) > 0) {
                                # get the variants we ordered
                                $variants = $item['reference']['options']['variants'];

                                # create the string describing the variants
                                $stockID = [];
                                foreach($variants as $variantID => $value) {
                                    $variantModel = Friluft\Variant::find($variantID);
                                    $variantString .= $variantModel->name .': ' .$value .', ';
                                    foreach($variantModel->data['values'] as $v) {
                                        if ($v['name'] == $value) $stockID[$value] = $v['id'];
                                    }
                                }
                                $stockID = implode('_', $stockID);
                                $stock = $product->variants_stock[$stockID];
                                $stock += $item['quantity'];
                            }
                            $variantString = rtrim($variantString, ', ');
						}
						?>
						<tr class="item" data-id="{{$id}}">
							<td>
								<div style="width: 24px; height: 24px; margin: 4px; border: 1px solid #333; border-radius: 2px;"></div>
							</td>
							<td>
								<strong>{{$manufacturer}}</strong>
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