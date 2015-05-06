@extends('master')

@section('body')
<div id="splash" class="module">
	<header class="module-header">
		@yield('header')
	</header>
	<article class="module-content">
		@yield('content')
	</article>
</div>
@stop