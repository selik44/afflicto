@extends('admin.layout')

@section('title')
	Orders - @parent
@stop

@section('page')
	<h2>Order #{{$order->id}}</h2>
	<div id="orders-edit">

		{!! Former::open()
			->method('PUT')
			->action(route('admin.orders.update', $order))
			->rules([])
		!!}

		<div id="orderdata" class="module">
			<div class="module-header clearfix">
				<h6 class="pull-left">Order Data</h6>
				<div class="button-group pull-right">
					<a class="button large" href="{{route('admin.orders.packlist', $order)}}"><i class="fa fa-list-ol"></i> Pack List</a>
				</div>
			</div>

			<div class="module-content">
				<div class="col-xs-12 tight">
					<div class="col-m-4 tight-left">
						<h6 class="end">Client</h6>
						<address>
							<strong>Name: </strong> <a href="{{route('admin.users.edit', $order->user)}}">{{$order->user->name}}</a><br>
							<strong>Email: </strong> {{$order->user->email}}<br>
							<strong>ID: </strong> {{$order->user->id}}
						</address>
					</div>

					<div class="col-m-4">
						<h6 class="end">Billing Address</h6>
						<address>
							{{$order->billing_address['given_name']}}<br>
							{{$order->billing_address['street_address']}}, {{$order->billing_address['postal_code']}}
							{{$order->billing_address['city']}}, {{$order->billing_address['country']}}.
						</address>
					</div>

					<div class="col-m-4 tight-right">
						<h6 class="end">Shipping Address</h6>
						<address>
							{{$order->shipping_address['given_name']}}<br>
							{{$order->shipping_address['street_address']}}, {{$order->shipping_address['postal_code']}}
							{{$order->shipping_address['city']}}, {{$order->shipping_address['country']}}.
						</address>
					</div>
				</div>
			</div>

			<div class="col-xs-12">
				<p>
					<strong>Klarna Status:</strong> {{$order->klarna_status}}<br>
                    @if($order->activated)
                        <span class="color-success">Activated</span>
                    @else
                        <span class="color-error">NOT Activated</span>
                    @endif
				</p>
			</div>
		</div>

		<hr/>

		<div id="items" class="module">
			<div class="module-header clearfix">
				<h6 class="end pull-left">Products</h6>
                <a href="{{route('admin.orders.edit.products', $order->id)}}" class="button primary large pull-right"><i class="fa fa-pencil"></i> Edit</a>
			</div>
			<div class="module-content">
				<table class="bordered">
					<thead>
						<tr>
							<th>Product</th>
							<th>Quantity</th>
							<th>Sub-total</th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<th colspan="3">
								Total Eks/Ink: {{$order->total_price_excluding_tax / 100}} / {{$order->total_price_including_tax / 100}}
							</th>
						</tr>
						<tr>
							<th colspan="3">
								Total tax: {{$order->total_tax_amount}}
							</th>
						</tr>
					</tfoot>

					<tbody>
						@foreach($items as $id => $item)
							<?php
								if ($item['type'] != 'shipping_fee') {
									# get product ID and model
                                    $productID = $item['reference']['id'];
                                    $product = Friluft\Product::find($productID);

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
                                    }
                                    $variantString = rtrim($variantString, ', ');

                                    if (strlen($variantString) > 0) $variantString = ' [' .$variantString .']';

                                    # color the item by stock
                                    $class = 'color-success';
                                    if ($stock < $item['quantity']) $class = 'color-error';

                                    $title = '<span class="' .$class .'">' .$name .$variantString .' (' .$stock .'/' .$item['quantity'] .' in stock)</span>';
								}
							?>
							<tr class="item {{$class}}" data-id="{{$id}}">
								<td>
									{!! $title !!} &nbsp;&nbsp;<a target="_blank" href="{{url($product->getPath())}}"><i style="color: white;" class="fa fa-link color-white"></i></a>
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

		<hr/>

        <div id="events" class="module">
            <header class="module-header">
                <h6 class="end">Events</h6>
            </header>

            <div class="module-content">
                <table class="table bordered">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Event</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderEvents as $event)
                            <tr>
                                <td>{{$event->created_at->diffForHumans()}}</td>
                                <td>{{$event->comment}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <hr>

		<div id="status" class="module">
			<header class="module-header">
				<h6 class="end">Status</h6>
			</header>
			<div class="module-content">

				{!! Former::select('status')->options([
					'unprocessed' => 'Ubehandlet',
					'written_out' => 'Skrevet ut',
					'delivered' => 'Levert',
					'cancelled' => 'Kansellert',
					'ready_for_sending' => 'Klar til Sending',
					'processed' => 'Behandlet',
					'restorder' => 'Restordre',
				], $order->status) !!}
			</div>
		</div>

		<br>

		{!! Former::submit('Save')->class('primary submit large') !!}

		{!!Former::close() !!}
	</div>
@stop