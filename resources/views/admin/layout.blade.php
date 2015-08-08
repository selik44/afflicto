@extends('master')

@section('title')
	Admin - @parent
@stop

@section('body')
	<div id="admin">
		<header id="header">
			@section('header')
				<ul id="nav-top" class="nav end">
                    <li>
                        <a href="/">View Site</a>
                    </li>
                    @foreach(\Friluft\Store::all() as $store)
                        @if($store->name == \Friluft\Store::current()->name)
                            <li class="pull-right current"><a href="http://{{$store->host}}.tk/{{Request::path()}}">{{$store->name}}</a></li>
                        @else
                            <li class="pull-right"><a href="http://{{$store->host}}.tk/{{Request::path()}}">{{$store->name}}</a></li>
                        @endif
                    @endforeach
				</ul>

                <div id="nav" class="clearfix">
                    @include('admin.partial.nav')
                </div>
			@show
		</header>
		
		<div id="page">
			<div class="inner">
				@include('partial.alerts')
				@yield('page')
			</div>
		</div>
	</div>
@stop