@extends('front.layout')

@section('title')
    @lang('store.user.my account') - @parent
@stop

@section('aside')
    @include('front.partial.user_menu')
@stop

@section('article')
    <div class="row paper" style="padding: 1rem;">
        @include('front.partial.user_menu', ['horizontal' => true])
        <h2>@lang('store.my orders')</h2>
        <table class="table">
            <tbody>
            @foreach(Auth::user()->orders()->orderBy('created_at', 'desc')->get() as $order)
                <tr>
                    <th>{{$order->created_at->diffForHumans()}}</th>
                    <td><a href="{{route('user.order', ['order' => $order->id])}}">{{$order->getHumanName()}}</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@stop