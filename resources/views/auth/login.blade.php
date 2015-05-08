@extends('splash')

@section('title')
	Login - @parent
@stop

@section('header')
	<h2>Log in</h2>
@stop

@section('content')
	@if(Auth::user())
		<p class="lead">
			You're already logged in. <a href="{{url('user/logout')}}">Log out</a>.
		</p>
	@else
		<form class="vertical" method="POST" action="{{ url('/user/login') }}">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
			
			<div class="form-group">
				<label for="email">Email</label>
				<input id="email" type="email" name="email" value="{{ old('email') }}">
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" name="password">
			</div>
			
			<div class="form-group">
				<label class="checkbox-container" for="remember">Remember me
					<div class="checkbox">
						<input id="remember" type="checkbox">
						<span></span>
					</div>
				</label>
			</div>
			
			<input type="submit" value="Log in" name="login" class="end primary large"> <a href="{{route('user.forgot')}}">Forgot Your Password?</a>
		</form>
	@endif
@stop

@section('footer')
	<div style="padding: 1rem;">
		<a href="{{url('user/register')}}">Create Account</a>
	</div>
@stop