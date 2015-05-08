@extends('splash')

@section('title')
	Forgot Password - @parent
@stop

@section('header')
	<h2>Forgot Password</h2>
@stop

@section('content')
	{!! Former::open(url('user/forgot'))
		->method('post')
		->addClass('vertical clearfix')
		->rules([
			'email' => 'email|required',
		])
	!!}
	
	{!! Former::email('email', 'E-Mail Address') !!}

	
	{!! Former::submit('Reset')->addClass('primary large') !!}
		
	{!! Former::close() !!}

	</form>
@endsection