@extends('admin.layout')

@section('title')
	@lang('admin.coupons') - @parent
@stop

@section('header')
	<h3 class="title">@lang('admin.coupons')</h3>
@stop

@section('content')
	{!! $table !!}
@stop

@section('footer')
	{!! $pagination !!}
@stop