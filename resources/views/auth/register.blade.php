@extends('splash')

@section('title')
	@lang('store.auth.register') - @parent
@stop

@section('header')
	<h2>@lang('store.auth.register')</h2>
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
            {!! Former::text('firstname') !!}
        </div>
        <div class="col-sm-6 tight-right">
            {!! Former::text('lastname') !!}
        </div>
    </div>

    {!! Former::text('email') !!}

    {!! Former::password('password') !!}
    {!! Former::password('password_confirmation') !!}

	<hr>

	{!! Former::submit('Register')->addClass('large primary') !!}

	{!! Former::close() !!}
@stop