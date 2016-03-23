@extends('admin.layout')

@section('title')
	Endre Størrelse-kart - @parent
@stop

@section('header')
	<h2 class="title">Endre Størrelse-kart</h2>
@stop

@section('content')
	{!! Former::open_for_files()
		->method('PUT')
		->action(route('admin.sizemaps.update', $sizemap))
		->rules([
			'name' => 'required|max:255',
		]);
	 !!}

	{!! Former::text('name') !!}

	{!! Former::file('image') !!}

	{!! Former::submit('Lagre')->class('large success') !!}
	{!! Former::close() !!}

@stop

@section('footer')
@stop