@extends('emails.master')

@section('header')
    <h2>An account has been created for you.</h2>
@stop

@section('content')
    <h4>Thank you for your purchase!</h4>
    <p>For your convinience, we have created an account for you. To log in, use your email address and the following password: <code>{{$password}}</code>.</p>
    <p>Once logged in, you can change your password on the account settings page.</p>
    <hr>
    <p><a href="{{url('user/login')}}">Click here to log in.</a></p>
@stop