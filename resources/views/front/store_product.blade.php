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
				@foreach($product->images as $image)
					<div class="slide" style="background-image: url('{{asset('images/products/' .$image->name)}}');"></div>
				@endforeach
				</div>
			</div>
		</div>

		<div class="product-actions col-l-4 col-m-5 col-sm-6 tight-right">

			<form class="vertical" action="{{url('cart')}}" method="POST">
				<input type="hidden" name="_token" value="{{csrf_token()}}">
				<input type="hidden" name="product_id" value="{{$product->id}}">

                <h3>kr {{$product->price * $product->vatgroup->amount}},-</h3>

                <div class="product-variants">
                    @foreach($product->variants as $variant)
                        <div class="variant" data-id="{{$variant->id}}">
                            <label for="variant-{{$variant->id}}">{{$variant->name}}</label>
                            <select name="variant-{{$variant->id}}">
                                @foreach($variant->data['values'] as $name => $option)
                                    <option value="{{$name}}">{{$name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

				<br>

				<input class="large primary huge" style="width: 100%;" type="submit" name="Buy" value="@lang('store.buy')" class="success">
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