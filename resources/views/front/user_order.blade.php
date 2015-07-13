@extends('front.layout')

@section('title')
    Order #{{$order->id}} - Account - @parent
@stop

@section('breadcrumbs')
    {!! Breadcrumbs::render('user') !!}
@stop

@section('article')
    <h2 class="end">Order #{{$order->id}}</h2>

    <hr style="margin-top: 0px">

    <div class="module order-items">
        <header class="module-header">
            <h6 class="end">Items</h6>
        </header>

        <article class="module-content">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>

                <tfoot>
                    <tr>
                        <th colspan="3">Total: {{$order->total_price_including_tax / 100}}</th>
                    </tr>
                </tfoot>

                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <>
                                @if($item['name'] == 'shipping_costs')
                                    Shipping
                                @else
                                    {{$item['name']}}
                                @endif
                            </td>
                            <td>{{$item['quantity']}}</td>
                            <td>{{$item['total_price_including_tax'] / 100}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </article>
    </div>

@stop