@extends('splash')

@section('title')
	@lang('store.log in') - @parent
@stop

@section('header')
	<h2>@lang('store.log in')</h2>
@stop

@section('content')
	@if(Auth::user())
		<p class="lead">
			@lang('store.auth.already logged in') <a href="{{route('user.logout')}}">Log out</a>.
		</p>
	@else
		<form class="vertical" method="POST" action="{{ route('user.login') }}">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
			
			<div class="form-group">
				<label for="email">@lang('validation.attributes.email')</label>
				<input id="email" type="email" name="email" value="{{ old('email') }}">
			</div>

			<div class="form-group">
				<label for="password">@lang('validation.attributes.password')</label>
				<input type="password" name="password">
			</div>
			
			<div class="form-group">
				<label class="checkbox-container" for="remember">@lang('validation.attributes.remember')
					<div class="checkbox">
						<input id="remember" type="checkbox">
						<span></span>
					</div>
				</label>
			</div>
			
			<input type="submit" value="Log in" name="login" class="end primary large"> <a href="{{route('user.forgot')}}">@lang('store.auth.forgot your password?')</a>
		</form>
	@endif
@stop

@section('footer')
	<div style="padding: 1rem;">
		<a href="{{url('user/register')}}">@lang('store.auth.create account')</a>
	</div>
@stop