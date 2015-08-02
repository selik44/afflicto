@extends('splash')

@section('title')
	@lang('store.auth.reset password') - @parent
@stop

@section('content')
	<h2>@lang('store.auth.reset password')</h2>
	{!! Former::open(route('user.reset.post'))
	    ->method('POST')
		->addClass('vertical clearfix')
		->rules([
			'email' => 'email|required',
			'password' => 'required',
			'password_confirmation' => 'required',
		])
	!!}

	{!! Former::hidden('token')->value($token) !!}

	{!! Former::password('password', trans('store.auth.new password')) !!}

	{!! Former::password('password_confirmation') !!}
	
	{!! Former::submit('Submit')->addClass('primary large') !!}
		
	{!! Former::close() !!}

	</form>
@endsection