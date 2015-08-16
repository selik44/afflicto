@extends('admin.layout')

@section('title')
	Orders - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.order') #{{$order->id}}</h3>
@stop

@section('content')
	<div id="orders-edit">
		{!! Former::open()
			->method('PUT')
			->action(route('admin.orders.update', $order))
			->rules([])
		!!}

		<div id="orderdata" class="module">
            <div class="module-header clearfix">
                <h6 class="end pull-left">@lang('admin.user')'</h6>
            </div>
			<div class="module-content">
				<div class="col-xs-12 tight">
					<div class="col-m-4 tight-left">
                        {!! Former::select('user')->fromQuery($users, 'name', 'id') !!}

                        <p>
                            <strong>Klarna Status:</strong> {{$order->klarna_status}}<br>

                            <strong>@lang('admin.activated'): </strong>
                            @if($order->activated)
                                <span class="color-success">@lang('admin.yes')</span>
                            @else
                                <span class="color-error">@lang('admin.no')</span>
                            @endif
                        </p>
					</div>

					<div class="col-m-4">
						<h6 class="end">@lang('admin.billing address')</h6>
                        {!! Former::text('billing_name')->value($order->billing_address['given_name']) !!}

                        {!! Former::text('billing_street_address')->value($order->billing_address['street_address']) !!}

                        {!! Former::text('billing_postal_code')->value($order->billing_address['postal_code']) !!}

                        {!! Former::text('billing_city')->value($order->billing_address['city']) !!}

                        {!! Former::text('billing_country')->value($order->billing_address['country']) !!}

                        {!! Former::text('billing_phone')->value($order->billing_address['phone']) !!}
					</div>

                    <div class="col-m-4 tight-right">
                        <h6 class="end">@lang('shipping address')</h6>
                        {!! Former::text('shipping_name')->value($order->shipping_address['given_name']) !!}

                        {!! Former::text('shipping_street_address')->value($order->shipping_address['street_address']) !!}

                        {!! Former::text('shipping_postal_code')->value($order->shipping_address['postal_code']) !!}

                        {!! Former::text('shipping_city')->value($order->shipping_address['city']) !!}

                        {!! Former::text('shipping_country')->value($order->shipping_address['country']) !!}

                        {!! Former::text('shipping_phone')->value($order->shipping_address['phone']) !!}
                    </div>
				</div>
			</div>
		</div>

		<hr/>

		<div id="items" class="module">
			<div class="module-header clearfix">
				<h6 class="end pull-left">@lang('admin.products')</h6>
                <a href="{{route('admin.orders.edit.products', $order->id)}}" class="button primary large pull-right"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
			</div>
			<div class="module-content">
				<table class="bordered">
					<thead>
						<tr>
							<th>@lang('admin.product')</th>
							<th>@lang('admin.quantity')</th>
							<th>Pris</th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<th colspan="3">
								Total Eks/Ink: {{$order->total_price_excluding_tax}} / {{$order->total_price_including_tax}}
							</th>
						</tr>
						<tr>
							<th colspan="3">
								@lang('admin.total tax'): {{$order->total_tax_amount}}
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

                                        # add to variantString
                                        foreach($variants as $variantID => $value) {
                                            $variantModel = Friluft\Variant::find($variantID);
                                            $variantString .= $variantModel->name .': ' .$value .', ';
                                        }

                                        $stockID = implode('_', $item['reference']['options']['variants']);

                                        # get stock
                                        if ( ! isset($product->variants_stock[$stockID])) {
                                            $stock = 'error, invalid stock id: ' .$stockID;
                                        }else {
                                            $stock = $product->variants_stock[$stockID];
                                            $stock += $item['quantity'];
                                        }
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
								<td>kr {{$item['total_price_including_tax']}},-</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		<hr/>

        <div id="events" class="module">
            <header class="module-header">
                <h6 class="end">@lang('admin.events')'</h6>
            </header>

            <div class="module-content">
                <table class="table bordered">
                    <thead>
                        <tr>
                            <th>@lang('admin.when')</th>
                            <th>@lang('admin.event')</th>
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
                <div class="col-xs-6">
				{!! Former::select('status')->options([
					'unprocessed' => 'Ubehandlet',
					'written_out' => 'Skrevet ut',
					'delivered' => 'Levert',
					'cancelled' => 'Kansellert',
					'ready_for_sending' => 'Klar til Sending',
					'processed' => 'Behandlet',
					'unused' => 'Restordre',
				], $order->status) !!}
                </div>

                <div class="col-xs-6">
                    {!! Former::textarea('message') !!}
                    {!! Former::checkbox('notify_user') !!}
                </div>
			</div>
		</div>

		<br>
	</div>
@stop

@section('footer')
    <div class="button-group">
        {!! Former::submit(trans('admin.save'))->class('success submit large') !!}
        <a class="button large" href="{{route('admin.orders.packlist', $order)}}"><i class="fa fa-list-ol"></i> @lang('admin.pack list')</a>
    </div>
    {!!Former::close() !!}
@stop