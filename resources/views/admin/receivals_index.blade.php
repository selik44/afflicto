@extends('admin.layout')

@section('title')
	@lang('admin.receivals') - @parent
@stop

@section('header')
	<h3 class="title">@lang('admin.receivals')</h3>
	{!! $filters !!}
@stop

@section('content')
	{!! $table !!}
@stop

@section('footer')
	{!! $pagination !!}
@stop