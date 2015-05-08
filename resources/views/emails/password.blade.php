@extends('emails.master')

@section('header')
<h2>A Password reset has been requested on your account.</h2>
@stop

@section('content')
	<p>To reset your password, <a href="{{url('user/reset/' .$token)}}">click here</a>, or copy and paste the below link into your browser:</p>
	<code>{{url('user/reset/' .$token)}}</code>
	<hr>
	<p class="small">If you did not issue this request, you can simply ignore this email.</p>
@stop