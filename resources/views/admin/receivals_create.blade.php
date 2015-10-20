@extends('admin.layout')

@section('title')
	@lang('admin.new') - @lang('admin.receivals') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.receivals') <small>@lang('admin.new')</small></h3>
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