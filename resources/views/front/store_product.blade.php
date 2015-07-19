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
                    @if($product->manufacturer)
					    <strong>{{$product->manufacturer->name}}</strong>
                    @endif
                    <span class="title">{{$product->name}}</span>
				</h2>
			</div>
			<h1 class="price end pull-right"><strong>{{ceil($product->price * $product->vatgroup->amount)}},-</strong></h1>
		</header>

		<div class="product-top col-xs-12 tight">
			<div class="product-images col-l-8 col-m-7 col-m-12 tight-left">
                <div class="thumbnails">
                    @foreach($product->images as $key => $image)
                        <a class="thumbnail" href="#" data-slide="{{$key+1}}" style="background-image: url('{{asset('images/products/' .$image->name)}}');"></a>
                    @endforeach
                </div>
				<div class="slider contain">
					<div class="container">
					@foreach($product->images as $key => $image)
						<div class="slide" style="background-image: url('{{asset('images/products/' .$image->name)}}');"></div>
					@endforeach
					</div>
				</div>
			</div>

            <div class="product-summary hidden-l-up">
                <div class="lead">
                    {!! $product->summary !!}
                </div>
            </div>

			<div class="product-info col-l-4 col-m-12 tight-right">
                <div class="inner">
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

                        <button class="huge primary buy" style="width: 100%;" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
                    </form>

                    <div class="lead summary">
                        {!! $product->summary !!}
                    </div>
                </div>
			</div>
		</div>

		<hr>

		<div class="product-bottom col-xs-12 tight">
			<ul id="product-tabs" class="nav tabs clearfix">
				<li class="current"><a href="#product-info">@lang('store.product info')</a></li>
				<li><a href="#product-relations">@lang('store.related products')</a></li>

                @foreach($product->producttabs as $tab)
                    <li><a href="#product-tab-{{$tab->id}}">{{$tab->title}}</a></li>
                @endforeach
			</ul>

            @foreach($product->producttabs as $tab)
                <div class="tab" id="product-tab-{{$tab->id}}">
                    {!! $tab->body !!}
                </div>
            @endforeach

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
											<img class="image" src="{{$related->getImageURL()}}" alt=""/>
										@endif
									</a>
								</div>
								<div class="info pull-left">
									<a class="link" href="{{$related->getPath()}}">
										<h4><strong>{{$related->manufacturer->name}}</strong> {{$product->name}}</h4>
									</a>

									<div class="price">
										<h4><strong>{{ceil($related->price * $related->vatgroup->amount)}},-</strong></h4>
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
        var slider = $(".product-images .slider");
        var thumbnails = $(".product-images .thumbnails");

		slider.friluftSlider({
			delay: 4000,
			transitionSpeed: 400,
            slideLinks: false,
		});

        $(".product-images .thumbnails .thumbnail").click(function() {
            var id = $(this).attr('data-slide');
            slider.friluftSlider("goTo", id);
            slider.friluftSlider("stop");
            $(".product-images .thumbnails .thumbnail.active").removeClass('active');
            $(this).addClass('active');
        });

        slider.on('slider.next', function() {
            var id = slider.data('friluftSlider').currentIndex;

            $('.product-images .thumbnails .thumbnail.active').removeClass('active');

            $('.product-images .thumbnails .thumbnail[data-slide="' + id + '"]').addClass('active');
        });
	</script>
@stop