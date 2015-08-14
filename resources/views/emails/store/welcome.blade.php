@extends('emails.master')

@section('header')
    <h2>@lang('emails.welcome.header', ['store' => Friluft\Store::current()->name]).</h2>
@stop

@section('content')
    @lang('emails.welcome.message', ['password' => $password])
    <hr>
@stop

@section('footer')
    <a href="{{url('user/login')}}">@lang('emails.welcome.login')</a>
@stop