@extends('admin.layout')

@section('title')
	Størrelse-kart - @parent
@stop

@section('header')
	<h2 class="title">Størrelse-kart</h2>
@stop

@section('content')
	{!! $table !!}
@stop

@section('footer')
	{!! $pagination !!}
@stop