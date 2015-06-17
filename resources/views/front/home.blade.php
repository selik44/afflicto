@extends('front.layout')

@section('breadcrumbs')
	{!! Breadcrumbs::render('home') !!}
@stop

@section('slider')
	<div class="slider">
		<div class="container">
			<div style="background-image: url('{{asset('/images/slides/highpulse_compression.jpg')}}');" class="slide"></div>
			<div style="background-image: url('{{asset('/images/slides/neon_compression.jpg')}}');" class="slide"></div>
		</div>
	</div>
@stop


@section('scripts')
	@parent

	<script>
		$("#slider .slider").friluftSlider({
			delay: 4000,
			transitionSpeed: 600,
			autoHeight: true,
			heightRatio: 7 / 16
		});
	</script>
@stop


@section('article')
	<h2>@lang('store.popular')</h2>
	<div class="row" id="popular">
		<?php $i = 0; ?>
		@foreach(\Friluft\Product::all()->take(5) as $product)
			<div class="product col-xs-12 col-sm-6 col-m-4 col-l-3">
				<a style="background-size: contain; background-position: center; background-repeat: no-repeat; display: block; width: 100%; height: 200px; background-image: url('{{$product->getImageURL()}}');" class="thumbnail" href="{{$product->getPath()}}"></a>

				<h4 class="title text-center">

				</h4>

				<a class="link" href="{{$product->getPath()}}" style="text-decoration: none;">
					<h4 class="end text-uppercase text-center">{{$product->manufacturer->name}}</h4>
					<h5 class="end text-center">{{$product->name}}</h5>
				</a>

			<?php $i++;?>
			</div>
		@endforeach
	</div>

	<hr style="margin-top: 1rem;"/>

	<div class="row">
		<div class="col-xs-6 tight">
			<a href="#">
				<img src="http://lorempixel.com/600/600/abstract"/>
			</a>
		</div>

		<div class="col-xs-6 tight">
			<a href="#">
				<img src="http://lorempixel.com/600/600/sports"/>
			</a>
		</div>
	</div>
@stop