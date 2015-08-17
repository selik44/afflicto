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

        <table class="bordered boxed">
            <thead>
            <tr>
                <th>@lang('store.product')</th>
                <th>@lang('store.quantity')</th>
                <th>@lang('store.price')</th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <th colspan="3" style="text-align: right;"><h5>@lang('store.total'): {{$order->total_price_including_tax}},-</h5></th>
            </tr>
            </tfoot>

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
                            <h5 class="name">{{$item['name']}}</h5>
                            @if($model->variants->count() > 0)
                                <ul class="variants flat">
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
            </tbody>
        </table>

        <h5>Oppdateringer</h5>
        <table class="updates bordered">
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

