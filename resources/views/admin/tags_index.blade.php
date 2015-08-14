@extends('admin.layout')

@section('title')
	Tags - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.tags')</h3>
@stop

@section('content')
	{!! $table !!}
@stop

@section('footer')
    {!! $pagination !!}
@stop