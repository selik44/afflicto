@extends('emails.master')

@section('header')
    <h5>Exception: {{$exception->getMessage()}} @ {{$exception->getFile()}} : {{$exception->getLine()}}</h5>
@stop

@section('content')

    @if($exception instanceof \Klarna_Checkout_ApiErrorException)
        {{$exception->getPayload()}}
    @endif

    <h4>Request Input</h4>
    <table>
        <thead>
            <tr>
                <th colspan="2">Key</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($input as $key => $value)
                <tr>
                    <td>{{$key}}</td>
                    <td>=></td>
                    <td>{{var_export($value, true)}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>

    <h4>Other:</h4>
    <table>
        <tbody>
            <tr>
                <th>URL</th>
                <td>{{\Request::fullUrl()}}</td>
            </tr>
            <tr>
                <th>Route name</th>
                <?php
                    $route = \Request::route();
                    if (is_object($route)) {
                        $route = $route->getName();
                    }else {
                        $route = '';
                    }
                ?>
                <td>{{$route}}</td>
            </tr>
        </tbody>
    </table>

    <h4>Stack trace</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Call</th>
                <th>Args</th>
            </tr>
        </thead>
        <tbody>
        @foreach($exception->getTrace() as $key => $trace)
            <tr>
                <th>{{$key}}</th>
                <td>{{$trace['function']}} {{isset($trace['file']) ? 'in ' .$trace['file'] : ''}} {{isset($trace['line']) ? ' on line ' .$trace['line'] : ''}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop

@section('footer')
@stop