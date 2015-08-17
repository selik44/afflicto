@extends('front.layout')

@section('title')
    @lang('store.order') - @lang('store.my account') - @parent
@stop

@section('aside')
    @include('front.partial.user_menu')
@stop

@section('article')
    <div class="paper row" style="padding: 1rem;">
        <h2>@lang('store.order') {{$order->created_at->format('d M Y')}}</h2>
        <h4>Ordrenummer: #{{$order->id}}</h4>

        @if($order->status == 'delivered')
            <p class="lead color-success">@lang('store.order status.delievered')</p>
        @else
            <p class="lead color-error">@lang('store.order status.not delievered')</p>
        @endif

        <h4>Produkter</h4>
        <table class="bordered striped boxed">
            <thead>
                <tr>
                    <th>@lang('store.product')</th>
                    <th>@lang('store.quantity')</th>
                    <th>@lang('store.price')</th>
                </tr>
            </thead>

            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            @if($item['type'] == 'shipping_fee')
                                <h5 class="name">@lang('store.shipping.' .$item['name'])</h5>
                            @else
                                <?php
                                    $productID = $item['reference']['id'];
                                    $model = Friluft\Product::withTrashed()->find($productID);
                                ?>
                                <h5 class="name end">{{$item['name']}}</h5>
                                @if($model->variants->count() > 0)
                                    <ul class="variants flat end" style="margin-left: 1rem;">
                                        @foreach($item['reference']['options']['variants'] as $id => $value)
                                            <?php $variant = Friluft\Variant::find($id); ?>
                                            <li><strong>{{$variant->name}}: </strong> <span class="value">{{$variant->getValueName($value)}}</span></li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endif
                        </td>
                        <td>
                            {{$item['quantity']}}
                        </td>
                        <td>{{$item['total_price_including_tax']}},-</td>
                    </tr>
                @endforeach

                <tr class="total">
                    <td></td>
                    <td><h5 class="end pull-right">@lang('store.total'):</h5></td>
                    <th>{{$order->total_price_including_tax}},-</th>
                </tr>
            </tbody>
        </table>

        <br>

        <h4>Oppdateringer</h4>
        <table class="updates bordered boxed striped">
            <thead>
            <tr>
                <th>Når</th>
                <th>Kommentar</th>
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
@stop

