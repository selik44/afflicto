@extends('emails.master')

@section('header')
    <h2>Kontakt</h2>
@stop

@section('content')
    <table class="table">
        <tbody>
            <tr>
                <th>Navn</th>
                <td>{{$input['name']}}</td>
            </tr>

            <tr>
                <th>Telefon</th>
                <td>{{$input['phone']}}</td>
            </tr>

            <tr>
                <th>E-mail Addresse</th>
                <td>{{$input['email']}}</td>
            </tr>

            <tr>
                <th>Melding</th>
                <td>{{$input['message']}}</td>
            </tr>

            @if(isset($user_id))
            <tr>
                <th>Kundenummer</th>
                <td><a href="{{route('admin.users.edit', $user_id)}}">{{$input['user_id']}}</a></td>
            </tr>
            @endif

            @if(isset($order_id))
            <tr>
                <th>Ordrenummer</th>
                <td>
                    <a href="{{route('admin.orders.edit', $order_id)}}">{{$input['order_id']}}</a>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
@stop

@section('footer')
@stop