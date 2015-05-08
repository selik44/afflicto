@extends('master')

@section('body')
<div id="splash" class="module">
	<header class="module-header">
		@yield('header')
	</header>
	<article class="module-content">
		@include('partial.alerts')

		@yield('content')
	</article>
	<footer class="module-footer">
		@yield('footer')
	</footer>
</div>
@stop