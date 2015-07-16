@extends('admin.layout')

@section('title')
	Categories - @parent
@stop

@section('page')
	<h2>Categories</h2>
    {!! $filters !!}
    <hr class="small end">

	{!! $table !!}
    <hr class="end">
    {!! $pagination !!}
@stop