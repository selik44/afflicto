@extends('master')

@section('title')
	Admin - @parent
@stop

@section('body')
	<div id="admin">
		<header id="header">
			@section('header')
				<ul class="nav end">
				@foreach(\Friluft\Store::all() as $store)
					@if($store->name == \Friluft\Store::current()->name)
						<li class="current"><a href="http://{{$store->host}}.tk/{{Request::path()}}">{{$store->name}}</a></li>
					@else
						<li><a href="http://{{$store->host}}.tk/{{Request::path()}}">{{$store->name}}</a></li>
					@endif
				@endforeach
				</ul>
			@show
		</header>
		
		<aside id="aside">
			@section('aside')
				@include('admin.partial.nav')
			@show
		</aside>
		
		<div id="page">
			<div class="inner">
				@include('partial.alerts')
				@yield('page')
			</div>
		</div>
	</div>
@stop