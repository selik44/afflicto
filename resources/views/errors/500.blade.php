@extends('splash')

@section('title')
    500 - @parent
@stop

@section('header')
    <h2><i class="fa fa-warning color-error"></i> 500</h2>
@stop

@section('content')
    <h4>Oj, det gikk ikke så bra...</h4>
    <p>Feilen har blitt notert.</p>
    <a class="button primary large" href="{{route('home')}}">Til forsiden</a>
@stop