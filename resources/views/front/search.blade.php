@extends('front.layout')

@section('title')
	Search - @parent
@stop

@section('breadcrumbs')
	{!! Breadcrumbs::render('search') !!}
@stop

@section('article')
	<h2 class="end">Search</h2>
	<hr>
	@include('front.partial.products', $products)
@stop