@extends('front.layout')

@section('title')
    Account - @parent
@stop

@section('breadcrumbs')
    {!! Breadcrumbs::render('user') !!}
@stop

@section('article')
    <h2 class="end">My Account</h2>

    <hr style="margin-top: 0px">

    <div class="row user-grid">
        <div class="col-xs-6">
            <div class="module user-orders">
                <header class="module-header">
                    <h6 class="end">My Orders</h6>
                </header>

                <article class="module-content">
                    <table class="table">
                        <tbody>
                        @foreach(\Auth::user()->orders()->orderBy('created_at', 'desc')->get() as $order)
                            <tr>
                                <th>{{$order->created_at->diffForHumans()}}</th>
                                <td><a href="{{route('user.order', ['order' => $order->id])}}">Order #{{$order->id}}</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </article>
            </div>
        </div>
    </div>
@stop