@extends('admin.layout')

@section('title')
	@lang('admin.new receival') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.new receival')</h3>
@stop

@section('content')
	{!! Former::open()
		->method('POST')
		->action(route('admin.receivals.store'))
	 !!}

	{!!
		Former::select('manufacturer_id')->label('manufacturer')->fromQuery(\Friluft\Manufacturer::all(), 'name', 'id')
	!!}

	{!! Former::submit('create')->class('success') !!}

	{!! Former::close() !!}
@stop