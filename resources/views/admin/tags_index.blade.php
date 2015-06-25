@extends('admin.layout')

@section('title')
	Tags - @parent
@stop

@section('page')
	<h2>@lang('admin.tags')</h2>
	{!! $table !!}

	<hr/>

	{!! $pagination !!}
@stop