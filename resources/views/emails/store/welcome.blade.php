@extends('emails.master')

@section('content')
    @lang('emails.welcome.message', ['password' => $password, 'id' => $id])
    <hr>
@stop

@section('footer')
    <a href="{{url('user/login')}}">@lang('emails.welcome.login')</a>
@stop