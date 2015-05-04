@extends('admin.layout')

@section('title')
	Categories - @parent
@stop

@section('page')
	<h2>Product Categories</h2>
	{!! $table !!}
@stop