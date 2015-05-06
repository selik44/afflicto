@extends('splash')

@section('title')
	Login - @parent
@stop

@section('header')
	<h2>Log in</h2>
@stop

@section('content')
	<form method="POST" action="{{ url('/auth/login') }}">
		<input type="hidden" name="_token" value="{{csrf_token()}}">

		<label for="email">Email
			<input type="email" name="email" value="{{ old('email') }}">
		</label>

		<label for="password">Password
			<input type="password" name="password">
		</label>

		<label class="checkbox-container" for="remember">Remember me
			<div class="checkbox">
				<input id="remember" type="checkbox">
				<span></span>
			</div>
		</label>

		<input type="submit" value="Log in" name="login" class="end primary"> or <a href="{{url('auth/register')}}">Create Account</a>
	</form>
@stop