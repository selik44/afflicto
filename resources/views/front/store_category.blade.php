@extends('front.layout')

@section('title')
	{{{$category->name}}} - Store - @parent
@stop

@section('breadcrumbs', Breadcrumbs::render('category', $category))

@section('article')
	<h1 class="end">{{{$category->name}}}</h1>
	<hr style="margin-top: 0.5rem">
	

	@include('front.partial.products', ['products' => $category->nestedProducts()])

@stop

@section('aside')
	<div class="block store-menu visible-m-up">
		<ul id="store-menu" class="nav vertical fancy">
			<?php
			$c = '';
			if (Request::is($category->getRoot()->getPath())) {
				$c = 'current';
			}
			?>
			<li>
				<a class="{{{$c}}}" href="{{url('/store/' .$category->getRoot()->slug)}}">{{$category->getRoot()->name}}</a>
			</li>
			<?php
				echo $category->getRoot()->renderMenu('/store', 3);
			?>
		</ul>
	</div>
	@parent
@stop