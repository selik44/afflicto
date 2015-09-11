@extends('emails.master')

@section('content')
    <p class="lead">
        @lang('emails.order_confirmation.message', ['order' => $order->id])
    </p>
    <table style="width: 100%; text-align: left;">
        <thead>
            <tr>
                <th>@lang('store.products')</th>
                <th>@lang('store.quantity')</th>
                <th>@lang('store.price')</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="2"><h4 class="end">@lang('store.total')</h4></th>
                <th><h4 class="end">{{$order->total_price_including_tax}},-</h4></th>
            </tr>
        </tfoot>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    @if($item['type'] == 'shipping_fee')
                        <td colspan="2">@lang('store.shipping.' .$item['name'])</td>
                        <td>{{$item['total_price_including_tax']}},-</td>
                    @else
                        <td>{{$item['name']}}
							<?php
								$model = \Friluft\Product::find($item['reference']['id']);
							?>
							@if($model->hasVariants())
								<ul class="flat variants">
									@if($model->isCompound())
										@foreach($model->getChildren() as $child)
											@foreach($child->variants as $variant)
												<li><strong>{{$variant->name .' (' .$child->name .')'}}:</strong> <span>{{$variant->getValueName($item['reference']['options']['variants'][$variant->id]}}</span></li>
											@endforeach
										@endforeach
									@else
										@foreach($model->variants as $variant)
											<li><strong>{{$variant->name}}:</strong> <span>{{$variant->getValueName($item['reference']['options']['variants'][$variant->id])}}</span></li>
										@endforeach
									@endif
								</ul>
							@endif
                        </td>
                        <td>{{$item['quantity']}}</td>
                        <td>{{$item['total_price_including_tax']}},-</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@stop

@section('footer')
    @lang('emails.order_confirmation.footer', ['id' => $order->user->id]) <a href="{{route('user')}}">@lang('store.here')</a>.
@stop