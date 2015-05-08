@extends('admin.layout')

@section('title')
	Categories - @parent
@stop

@section('page')
	<h2>Categories</h2>
	{!! $table !!}

	<div class="footer-height-fix"></div>
	<footer id="footer">
		<div class="inner">
			{!! $pagination !!}
		</div>
	</footer>
@stop