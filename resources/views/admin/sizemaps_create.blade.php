@extends('admin.layout')

@section('title')
	Nytt Størrelse-kart - @parent
@stop

@section('header')
	<h2 class="title">Nytt Størrelse-kart</h2>
@stop

@section('content')
	{!! Former::open_for_files()
		->method('POST')
		->action(route('admin.sizemaps.store'))
		->rules([
			'name' => 'required|max:255',
			'image' => 'required',
		]);
	 !!}

	{!! Former::text('name') !!}

	{!! Former::file('image') !!}

	{!! Former::submit('Lagre')->class('large success') !!}
	{!! Former::close() !!}

@stop

@section('footer')
@stop