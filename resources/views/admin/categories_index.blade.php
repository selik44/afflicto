@extends('admin.layout')

@section('title')
	@lang('admin.categories') - @parent
@stop

@section('header')
    <h2 class="title">@lang('admin.categories')</h2>
    {!! $filters !!}
@stop

@section('content')
	{!! $table !!}
@stop

@section('footer')
    {!! $pagination !!}
@stop