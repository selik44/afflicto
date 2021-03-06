@extends('front.layout')

@section('title')
	{{{$category->name}}} - Store - @parent
@stop

@section('breadcrumbs', Breadcrumbs::render('category', $category))

<?php
$mostExpensive = 0;
foreach($products as $product) {
	if ($product->price > $mostExpensive) {
		$mostExpensive = $product->price;
	}
}
?>

@section('scripts')
	@parent
	<script type="text/javascript">
		var grid = $(".products-grid");
		var options = $(".products-grid-options");
		var priceSlider = options.find('.filters .price-filter .price-slider');
		var priceSliderMinValue = options.find('.filters .price-filter .min-value');
		var priceSliderMaxValue = options.find('.filters .price-filter .max-value');

		var manufacturersSelect = options.find('.filters .manufacturers-filter select');

		priceSlider.noUiSlider({
			start: [0, {{$mostExpensive}}],
			step: 100,
			range: {
				min: 0,
				max: {{$mostExpensive}}
			}
		});

		priceSlider.on({
			slide: function() {
				var value = priceSlider.val();
				priceSliderMinValue.text(parseInt(value[0]));
				priceSliderMaxValue.text(parseInt(value[1]));
			},

			change: function() {
				updateFilter();
			}
		});

		// manufacturers select
		manufacturersSelect.chosen().next().removeAttr('style').css('width', '100%');

		manufacturersSelect.change(function(e) {
			var filter = manufacturersSelect.val();
			if (filter == '*') {
				filter = null;
			}

			updateFilter();
		});

		function updateFilter() {
			grid.isotope({
				filter: function() {
					var price = $(this).attr('data-price');
					var manufacturer = $(this).attr('data-manufacturer');

					if (price < priceSlider.val()[0]) return false;

					if (price > priceSlider.val()[1]) return false;

					if (manufacturersSelect.val() !== '*' && manufacturer !== manufacturersSelect.val()) return false;

					return true;
				}
			});
		};

		// initialize isotope
		imagesLoaded(document.querySelector('.products-grid'), function() {
			grid.isotope({
				itemSelector: '.product',
				layoutMode: 'packery',
			});
		});
	</script>
@stop

@section('article')

	<div class="products-grid-options clearfix module">

		<header class="module-header clearfix">
			<h2 class="end pull-left">{{{ucwords(strtolower($category->name))}}}</h2>
		</header>

		<div class="module-content clearfix">
			<div class="filters clearfix">
				<div class="col-sm-12 col-m-6 filter price-filter">
					<div class="header">
						<h5 class="pull-left end">Price</h5>
					</div>
					<div class="values">
						<div class="pull-left min-value">0</div>
						<div class="pull-right max-value">{{$mostExpensive}}</div>
					</div>
					<div class="control">
						<div class="price-slider"></div>
					</div>
				</div>

				<div class="col-sm-12 col-m-6 filter manufacturers-filter">
					<h5>Manufacturer</h5>
					<select name="manufacturers-select" class="manufacturers-select">
						<option value="*">All</option>
						@foreach($manufacturers as $m)
							<option value="{{$m->id}}">{{$m->name}}</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>

	</div>

	<hr>

	<div class="products-grid">
		@foreach($products as $product)
			<div class="product" data-id="{{$product->id}}" data-price="{{$product->price}}" data-manufacturer="{{$product->manufacturer->id}}">
				<?php
				$img = $product->images()->first();
				if ($img == null) {
					$img = '';
				}else {
					$img = asset('images/products/' .$img->name);
				}

				$link = url($product->getPath());
				?>

				<div class="preview">
					<a href="{{$link}}" style="background-image: url('{{$img}}');" class="image">
					</a>
				</div>

				<header class="header clearfix">
					<div class="title">
						<h6 class="manufacturer end">{{$product->manufacturer->name}}</h6>
						<h5 class="name end">{{$product->name}}</h5>
					</div>

					<h3 class="price end">{{$product->price}},-</h3>
				</header>

				<footer class="footer">
					<hr class="divider shadow">
					<a class="buy" href="{{$link}}"><i class="fa fa-cart-plus"></i></a>
					<a class="share" href="#"><i class="fa fa-share-alt"></i></a>
				</footer>
			</div>
		@endforeach
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