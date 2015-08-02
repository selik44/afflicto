@extends('emails.master')

@section('header')
    <h2>@lang('emails.welcome.header').</h2>
@stop

@section('content')
    @lang('emails.welcome.message', ['password' => $password])
    <hr>
    <p><a href="{{url('user/login')}}">@lang('emails.welcome.login')</a></p>
@stop