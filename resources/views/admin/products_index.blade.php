@extends('admin.layout')

@section('title')
	Products - @parent
@stop

@section('page')
	<h2>Products</h2>
	{!! $table !!}
@stop