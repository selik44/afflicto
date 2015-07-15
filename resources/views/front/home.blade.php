@extends('front.layout')

@section('breadcrumbs')
	{!! Breadcrumbs::render('home') !!}
@stop

@section('slider')
	<div class="row end">
		<div class="left col-l-3 tight visible-l-up">
			<div class="image" style="width:100%; height:250px; background-image: url('http://lorempixel.com/300/200/technics'); background-size: cover; background-position: center;">
			</div>
			<div class="image" style="width:100%; height:250px; background-image: url('http://lorempixel.com/300/200/sports'); background-size: cover; background-position: center;">
			</div>
		</div>
		<div class="middle col-l-6 tight">
			<div class="slider">
				<div class="container">
                    @foreach(\Friluft\Image::whereType('slideshow')->orderBy('order', 'asc')->get() as $slide)
                        <div style="background-image: url('{{asset('/images/' .$slide->name)}}');" class="slide"></div>
                    @endforeach
				</div>
			</div>
		</div>
		<div class="tight col-l-3 tight visible-l-up">
			<div class="image" style="width:100%; height:250px; background-image: url('http://lorempixel.com/300/200/abstract'); background-size: cover; background-position: center;">
			</div>
			<div class="image" style="width:100%; height:250px; background-image: url('http://lorempixel.com/300/200/sports'); background-size: cover; background-position: center;">
			</div>
		</div>
	</div>
@stop


@section('scripts')
	@parent

	<script>
		$("#slider .slider").friluftSlider({
			delay: 4000,
			transitionSpeed: 600,
			autoHeight: false,
			heightRatio: 7 / 16
		});
	</script>
@stop


@section('article')
	<h2 class="end">@lang('store.popular')</h2>

	<hr style="margin-top: 0px">

	<div class="row products-popular">
		@foreach(\Friluft\Product::all()->take(4) as $product)
			<div class="col-xs-6 col-l-3">
				@include('front.partial.products-block', ['product' => $product])
			</div>
		@endforeach
	</div>

	<hr/>

	<div class="row">
		<div class="col-xs-6 tight">
			<a href="#">
				<img src="http://lorempixel.com/800/600/abstract"/>
			</a>
		</div>

		<div class="col-xs-6 tight">
			<a href="#">
				<img src="http://lorempixel.com/800/600/sports"/>
			</a>
		</div>
	</div>
@stop