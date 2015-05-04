@extends('front.store_category')


@section('title')
	{{{$product->name}}} - Store - @parent
@stop


@section('breadcrumbs', Breadcrumbs::render('product', $product, $category))


@section('article')
	<h1 class="end">{{{ucfirst(strtolower($product->name))}}}</h1>
	<hr style="margin-top: 0.5rem">
	
	<div class="slider product-images contain">
		<div class="container">
		@foreach($product->images as $index => $image)
			<div class="slide" style="background-image: url('{{url($product->getImagePath($index))}}');"></div>
		@endforeach
		</div>
	</div>
	
	<hr>

	<div class="product-description">
	{!! $product->description !!}
	</div>
@stop


@section('aside')
	@parent
@stop


@section('scripts')
	@parent

	<script type="text/javascript">
		$(".product-images").friluftSlider({
			delay: 4000,
			transitionSpeed: 600,
		});
	</script>
@stop