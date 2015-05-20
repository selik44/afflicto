@extends('splash')

@section('title')
	Register - @parent
@stop

@section('header')
	<h2>Register</h2>
@stop

@section('content')
	{!! Former::open(url('user/register'))
		->addClass('vertical clearfix')
		->rules([
			'email' => 'email,required',
			'password' => 'required|min:8',
		])
	!!}

    <div class="row clearfix">
        <div class="col-sm-6 tight-left">
            {!! Former::text('firstname', 'First Name') !!}
        </div>
        <div class="col-sm-6 tight-right">
            {!! Former::text('lastname', 'Last Name') !!}
        </div>
    </div>

    {!! Former::text('email', 'E-Mail Address') !!}

    {!! Former::password('password') !!}
    {!! Former::password('password_confirmation', 'Confirm Password') !!}


	<hr>

	{!! Former::submit('Register')->addClass('large primary') !!}

	{!! Former::close() !!}
@stop