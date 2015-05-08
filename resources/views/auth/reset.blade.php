@extends('splash')

@section('title')
	Reset Password - @parent
@stop

@section('content')
	<h2>Reset Password</h2>
	{!! Former::open(url('user/reset'))
		->addClass('vertical clearfix')
		->rules([
			'email' => 'email|required',
			'password' => 'required',
			'password_confirmation' => 'required',
		])
	!!}
	
	{!! Former::hidden('token')->value($token) !!}

	{!! Former::password('password', 'New Password') !!}

	{!! Former::password('password_confirmation', 'Confirm Password') !!}
	
	{!! Former::submit('Submit')->addClass('primary large') !!}
		
	{!! Former::close() !!}

	</form>
@endsection