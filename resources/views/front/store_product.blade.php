@extends('front.layout')


@section('title')
	{{{$product->name}}} - {{{$product->categories->first()->name}}} - @parent
@stop


@section('breadcrumbs', Breadcrumbs::render('product', $product, $category))

@section('article')

	<div class="product-view">
		<header class="header">
			<div class="title pull-left">
				<h2 class="manufacturer end">
					<strong>{{$product->manufacturer->name}}</strong> <span class="title">{{$product->name}}</span>
				</h2>
			</div>
			</h1>
			<h1 class="price end pull-right"><strong>{{$product->price}},-</strong></h1>
		</header>

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

			<div class="product-info col-l-4 col-m-5 col-sm-6 tight-right">
				<form class="vertical" action="{{url('cart')}}" method="POST">
					<input type="hidden" name="_token" value="{{csrf_token()}}">
					<input type="hidden" name="product_id" value="{{$product->id}}">
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

					<button class="huge primary huge buy" style="width: 100%;" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.buy')</button>
				</form>

				<p class="lead summary">
					{{$product->summary}}
				</p>
			</div>
		</div>

		<hr>

		<div class="product-bottom col-xs-12 tight">
			<ul id="product-tabs" class="nav tabs clearfix">
				<li class="current"><a href="#product-info">Product Info</a></li>
				<li><a href="#product-relations">Related Products</a></li>
			</ul>

			<div class="tab" id="product-info">
				{!! $product->description !!}
			</div>

			<div class="tab" id="product-relations">
				<ul class="relations flat">
					@foreach($product->relations as $related)
						<li class="related clearfix">
							<div class="row">
								<div class="thumbnail pull-left">
									<a href="{{$related->getPath()}}">
										@if($related->images)
											<img class="image" src="{{asset('images/products/' .$related->images[0]->name)}}" alt=""/>
										@else
									</a>
								</div>
								<div class="info pull-left">
									<a class="link" href="{{$related->getPath()}}">
										<h4><strong>{{$related->manufacturer->name}}</strong> {{$product->name}}</h4>
									</a>

									<div class="price">
										<h4><strong>{{$related->price}},-</strong></h4>
									</div>
								</div>
							</div>
						</li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
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


@section('scripts')
	@parent

	<script type="text/javascript">
		$(".product-images .slider").friluftSlider({
			delay: 4000,
			transitionSpeed: 600,
		});
	</script>
@stop