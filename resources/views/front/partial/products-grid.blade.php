@section('scripts')
	@parent
	<script type="text/javascript">
		(function() {
			// initialize isotope
			imagesLoaded(document.querySelector('.products-grid'), function() {
				$(".products-grid").isotope({
					itemSelector: '.product',
					layoutMode: 'packery'
				});
			});
		})();
	</script>
@endsection

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
				<div class="overlay">
					<div class="tags">
					@foreach($product->tags as $tag)
						<span class="tag"><i class="{{$tag->icon}}"></i> {{$tag->label}}</span>
					@endforeach
					</div>
				</div>
			</div>

			<header class="header clearfix">
				<div class="title">
					<h6 class="manufacturer end">{{$product->manufacturer->name}}</h6>
					<h5 class="name end">{{$product->name}}</h5>
				</div>

				<h3 class="price end">{{$product->price * $product->vatgroup->amount}},-</h3>
			</header>

			<footer class="footer">
				<hr class="divider shadow">
				<a class="buy" href="{{$link}}"><i class="fa fa-cart-plus"></i></a>
				<a class="share" href="#"><i class="fa fa-share-alt"></i></a>
			</footer>
		</div>
	@endforeach
</div>

@if(!isset($withMenu) || $withMenu)
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
	@endsection
@endif