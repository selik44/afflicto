@extends('admin.layout')

@section('title')
	Products - @parent
@stop

@section('page')
	<h2 class="end">Products</h2>
    {!! $filters !!}
    <hr>
	{!! $table !!}
    {!! $pagination !!}
@stop