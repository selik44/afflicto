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
		@include('front.partial.products-block', ['product' => $product])
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