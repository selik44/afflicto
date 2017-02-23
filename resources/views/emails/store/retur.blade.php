@extends('emails.master')

@section('header')
    <h2>Retur</h2>
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
            <th>Årsak</th>
            <td>
                @if($input['cause'] == 0)
                    Retur
                @elseif($input['cause'] == 1)
                    Bytte
                @elseif($input['cause'] == 2)
                    Reklamasjon
                @endif
            </td>
        </tr>

        <tr>
            <th>Varer</th>
            <td>{{$input['varer']}}</td>
        </tr>

        @if(isset($input['user_id']))
            <tr>
                <th>Kundenummer</th>
                <td><a href="{{route('admin.users.edit', $input['user_id'])}}">{{$input['user_id']}}</a></td>
            </tr>
        @endif

        @if(isset($input['order_id']))
            <tr>
                <th>Ordrenummer</th>
                <td>
                    <a href="{{route('admin.orders.edit', $input['order_id'])}}">{{$input['order_id']}}</a>
                </td>
            </tr>
        @endif
        </tbody>

        <tr>
            <th>Over 2kg?</th>
            <td>
                {{isset($input['over_2_kg']) ? 'Ja' : 'Nei'}}
            </td>
        </tr>
    </table>
@stop

@section('footer')
@stop