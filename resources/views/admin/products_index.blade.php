@extends('admin.layout')

@section('title')
	Products - @parent
@stop

@section('page')
	<h2>Products</h2>
	{!! $table !!}

	<div class="footer-height-fix"></div>
	<footer id="footer">
		<div class="inner">
			{!! $pagination !!}
		</div>
	</footer>
@stop