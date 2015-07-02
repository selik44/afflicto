@extends('front.layout')

@section('breadcrumbs')
	{!! Breadcrumbs::render('home') !!}
@stop

@section('slider')
	<div class="row end">
		<div class="left col-l-3 tight visible-l-up">
			<div class="image" style="width:100%; height:250px; background-image: url('http://lorempixel.com/800/500/technics'); background-size: cover; background-position: center;">
			</div>
			<div class="image" style="width:100%; height:250px; background-image: url('http://lorempixel.com/800/500/sports'); background-size: cover; background-position: center;">
			</div>
		</div>
		<div class="middle col-l-6 tight">
			<div class="slider">
				<div class="container">
					<div style="background-image: url('{{asset('/images/slides/highpulse_compression.jpg')}}');" class="slide"></div>
					<div style="background-image: url('{{asset('/images/slides/neon_compression.jpg')}}');" class="slide"></div>
				</div>
			</div>
		</div>
		<div class="tight col-l-3 tight visible-l-up">
			<div class="image" style="width:100%; height:250px; background-image: url('http://lorempixel.com/800/500/abstract'); background-size: cover; background-position: center;">
			</div>
			<div class="image" style="width:100%; height:250px; background-image: url('http://lorempixel.com/800/500/sports'); background-size: cover; background-position: center;">
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

	<hr/>

	<div class="row products-popular">
		@foreach(\Friluft\Product::all()->take(4) as $product)
			<div class="col-m-6 col-l-3 tight">
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