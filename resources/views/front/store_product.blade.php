@extends('front.layout')


@section('title')
	{{{$product->name}}} - {{{$product->categories->first()->name}}} - @parent
@stop


@section('breadcrumbs', Breadcrumbs::render('product', $product, $category))


@section('article')
	<h1 class="end">{{{ucwords(strtolower($product->name))}}}</h1>
	<hr style="margin-top: 0.5rem">
	
	<div class="product-top col-xs-12 tight">
		<div class="product-images col-l-8 col-m-7 col-sm-6 tight-left">
			<div class="slider contain">
				<div class="container">
				@foreach($product->images as $index => $image)
					<div class="slide" style="background-image: url('{{url($product->getImagePath($index))}}');"></div>
				@endforeach
				</div>
			</div>
		</div>

		<div class="product-actions col-l-4 col-m-5 col-sm-6 tight-right">
			<h3>Buy</h3>
			<form class="vertical" action="{{url('cart/add')}}" method="POST">
				<input type="hidden" name="_method" value="PUT">
				<input type="hidden" name="_token" value="{{csrf_token()}}">

				<input type="hidden" name="product_id" value="{{$product->id}}">
				
				<fieldset class="form-attribute">
					<label for="size">Size
						<select name="attribute_size">
							<option value="xs">XS</option>
							<option value="small">Small</option>
							<option value="medium">Medium</option>
							<option value="large">Large</option>
							<option value="xl">XL</option>
							<option value="xxl">XXL</option>
						</select>
					</label>
				</fieldset>

				<div class="button-group flex">
					<input type="submit" name="add-to-cart" value="@lang('store.add to cart')" class="primary">
					<input type="submit" name="Buy now" value="@lang('store.buy now')" class="success">
				</div>

			</form>
		</div>
	</div>	
	
	<hr>

	<div class="product-bottom col-xs-12 tight">
		<ul id="product-tabs" class="nav tabs clearfix">
			<li class="current"><a href="#product-info">Product Info</a></li>
			<li><a href="#product-related">Related Products</a></li>
			<li><a href="#product-reviews">Reviews</a></li>
		</ul>

		<div class="tab" id="product-info">
			{!! $product->description !!}
		</div>

		<div class="tab" id="product-related">
			<ul>
				<li>Something</li>
				<li>Else</li>
				<li>Here</li>
			</ul>
		</div>
		
		<div class="tab" id="product-reviews">
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur, ratione.</p>
		</div>
	</div>
@stop

@section('aside')
	<div class="block store-menu visible-m-up">
		<ul class="nav vertical fancy module">
			<?php
			$c = '';
			if (Request::is($category->getRoot()->getPath())) {
				$c = 'current';
			}
			?>
			<li>
				<a class="{{{$c}}}" href="{{url('/store/' .$category->getRoot()->slug)}}">Alt</a>
			</li>
			<?php
				echo $category->getRoot()->renderMenu('/store', 3);
			?>
		</ul>
	</div>
	@parent
@stop


@section('scripts')
	@parent

	<script type="text/javascript">
		$(".product-images .slider").friluftSlider({
			delay: 4000,
			transitionSpeed: 600,
		});
	</script>
@stop