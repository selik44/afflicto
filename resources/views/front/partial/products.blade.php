<div class="products-grid-options clearfix">
	<div class="col-xs-2 filter-header tight-left tight-vertical">
		<h5>Filter:</h5>
	</div>
	
	<div class="col-xs-10 filters tight-right tight-vertical">
		<div class="col-xs-6 filter-price">
			<div class="price-slider"></div>
		</div>

		<div class="col-xs-6 filter-attribute">
			
		</div>
	</div>
	
</div>

<hr>

<div class="products-grid">
	<?php
		$mostExpensive = 0;
		foreach($products as $product) {
			if ($product->price > $mostExpensive) {
				$mostExpensive = $product->price;
			}
		}
	?>

	@foreach($products as $product)
		<div class="product">
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
					<button data-product-id="{{{$product->id}}}" class="button large primary quick-peek end">
						<i class="fa fa-search"></i> @lang('Quick Peek')
					</button>
				</div>
			</div>

			<hr class="divider shadow">

			<footer class="footer">
				<a href="{{$link}}"><h5 class="title pull-left end">{{{$product->name}}}</h5></a>
				<span class="price pull-right">kr {{{$product->price}}},-</span>
			</footer>
		</div>
	@endforeach
</div>

<div class="modal-wrapper">
	<div id="product-modal" class="modal fade" style="max-width: 600px">
		<a href="#" class="modal-dismiss" data-toggle-modal="#product-modal"><i class="fa fa-close"></i></a>
		<header class="modal-header">
		</header>
		<article class="modal-content">
			
		</article>
		<footer class="modal-footer">
			
		</footer>
	</div>
</div>

@section('scripts')
	@parent

	<script type="text/javascript">
		// initialize filtering
		var options = $(".products-grid-options");
		options.find('.filter-price .price-slider').noUiSlider({
			start: [0, {{$mostExpensive}}],
			range: {
				min: 0, 
				max: {{$mostExpensive}},
			},
		});

		// initialize isotope
		imagesLoaded(document.querySelector('.products-grid'), function() {
			$(".products-grid").isotope({
				itemSelector: '.product',
				layoutMode: 'packery',
			});
		});


		//quick peek event listener
		var modal = $("#product-modal");
		$(".products-grid .product .quick-peek").click(function() {
			$.get(Friluft.URL + '/html/product' + '/' + $(this).attr('data-product-id'), function(response) {
				//make an dom element out of the html
				var content = $(response);

				//put the name in the modal header
				modal.find('.modal-header')
					.html(content.find('.product-name').detach());

				//put the controls (buy form) in the footer
				modal.find('.modal-footer')
					.html(content.find('.product-controls').detach());

				//insert the rest of the content in the modal-content
				modal.find('.modal-content').html(content);

				//show the modal
				modal.gsModal("show");
			});
		});
	</script>
@stop