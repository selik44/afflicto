@extends('admin.layout')

@section('title')
    Orders - @parent
@stop

@section('page')
    <h2 class="end">Order #{{$order->id}} - Products</h2>
    <hr>
    {!! Former::open()
        ->method('PUT')
        ->action(route('admin.orders.update', $order))
        ->rules([])
    !!}

    <table class="table bordered striped">
        <thead>
        <tr>
            <th>Product</th>
            <th>Options</th>
            <th>Quantity</th>
            <th>Sub total</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <?php
                if ($item['reference'] == 'SHIPPING') continue;
                $id = (int) $item['reference']['id'];
                $options = $item['reference']['options'];
                $model = Friluft\Product::find($id);
            ?>
            <tr class="item">
                <td class="product">
                    <a href="{{url($model->getPath())}}" target="_blank">
                     {{$model->name}}
                    </a>
                </td>
                <td class="options">
                    <div class="variants">
                    @foreach($model->variants as $variant)
                        <div class="variant" data-id="{{$variant->id}}">
                            <select name="variant-{{$variant->id}}">
                                @foreach($variant->values as $value)
                                    <option value="{{$value['id']}}">{{$value['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                    </div>
                </td>
                <td class="quantity">
                    <input style="max-width: 80px;" type="text" name="quantity" value="{{$item['quantity']}}">
                </td>
                <td class="subtotal">
                    <h6><span class="value">{{$item['total_price_including_tax'] / 100}}</span>,-</h6>
                </td>
                <td class="actions">
                    <div class="pull-right button-group">
                        <button class="small edit primary"><i class="fa fa-pencil"></i> Edit</button>
                        <button class="small remove error"><i class="fa fa-close"></i> Remove</button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop