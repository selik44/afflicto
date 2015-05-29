@extends('emails.master')

@section('header')
    <h2>Your order has been received.</h2>
@stop

@section('content')
    <h4>Thank you for your purchase!</h4>
    <p>Here is a summary of your order:</p>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="2">Total</th>
                <th>{{$total}}</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach($items as $item)
                <tr>
                    @if($item['type'] == 'shipping_fee')
                        <td colspan="2">Shipping Costs</td>
                        <td>{{$item['unit_price']}}</td>
                    @else
                        <td>{{$item['name']}}
                            <ul class="variants">
                                <?php
                                    $model = \Friluft\Product::find($item['reference']['id']);
                                ?>
                                @foreach($model->variants as $variant)
                                    <li><strong>{{$variant->name}}:</strong> <span>{{$item['reference']['options']['variants'][$variant->id]}}</span></li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{$item['quantity']}}</td>
                        <td>{{$item['unit_price'] * $item['quantity']}}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr/>

    <p>You can view your orders <a href="{{url('user.orders')}}">here</a>.</p>
@stop