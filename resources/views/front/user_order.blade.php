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

        @if($order->status == 'delivered')
            <p class="lead color-success">@lang('store.order status.delievered')</p>
        @else
            <p class="lead color-error">@lang('store.order status.not delievered')</p>
        @endif

        <table class="bordered">
            <thead>
            <tr>
                <th>@lang('store.product')</th>
                <th>@lang('store.quantity')</th>
                <th>@lang('store.price')</th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <th colspan="3" style="text-align: right;"><h4>@lang('store.total'): {{$order->total_price_including_tax}},-</h4></th>
            </tr>
            </tfoot>

            <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        @if($item['type'] == 'shiping_fee')
                            @lang('store.shipping.' .$item['name'])
                        @else
                            {{$item['name']}}
                        @endif
                    </td>
                    <td>{{$item['quantity']}}</td>
                    <td>{{$item['total_price_including_tax']}},-</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@stop