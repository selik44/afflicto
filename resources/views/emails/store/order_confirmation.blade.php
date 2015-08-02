@extends('emails.master')

@section('header')
    <h3 class="end">@lang('emails.order_confirmation.header')</h3>
@stop

@section('content')
    <p class="lead">
        @lang('emails.order_confirmation.intro', ['id' => $order->id])
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
                        <td>{{$item['unit_price']}},-</td>
                    @else
                        <td>{{$item['name']}}
                            <ul class="flat variants">
                                <?php
                                    $model = \Friluft\Product::find($item['reference']['id']);
                                ?>
                                @foreach($model->variants as $variant)
                                    <li><strong>{{$variant->name}}:</strong> <span>{{$item['reference']['options']['variants'][$variant->id]}}</span></li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{$item['quantity']}}</td>
                        <td>{{($item['unit_price']) * $item['quantity']}},-</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@stop

@section('footer')
    @lang('emails.order_confirmation.footer') <a href="{{route('user')}}">@lang('store.here')</a>.
@stop