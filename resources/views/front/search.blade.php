@extends('front.layout')

@section('title')
	Search - @parent
@stop

@section('breadcrumbs')
	{!! Breadcrumbs::render('search') !!}
@stop

@section('article')
	<p>Search Results For <code>{{Input::get('terms')}}</code>:</p>
	<div class="search-results">
		<ul class="flat end">
			@if(count($products) > 0)
				<li>{{count($products)}} Products.</li>
			@endif

			@if(count($categories) > 0)
				<li>{{count($categories)}} Categories.</li>
			@endif

			@if(count($manufacturers) > 0)
				<li>{{count($manufacturers)}} Manufacturers.</li>
			@endif
		</ul>
		<hr/>
	</div>
	@if(count($products) == 0 && count($manufacturers) == 0 && count($categories) == 0)
		<p class="lead">
			Sorry, I could not find anything.
		</p>
	@else

		@if(count($manufacturers) > 0)
			<h4>Manufacturers</h4>
			<ul class="flat" id="manufacturers">
			@foreach($manufacturers as $manufacturer)
				<li><a href="{{route('store.manufacturer', $manufacturer->slug)}}">{{$manufacturer->name}}</a></li>
			@endforeach
			</ul>
			<hr>
		@endif

		@if(count($categories) > 0)
			<h4>Categories</h4>
			<ul class="flat"></ul>
			@foreach($categories as $cat)
				<li><a href="{{$cat->getPath()}}">{{$cat->name}}</a></li>
			@endforeach
		@endif

		@if(count($products) > 0)
			<h4>Products</h4>
			@include('front.partial.products-grid', ['products' => $products, 'withMenu' => false])
		@endif
	@endif
@stop