<div class="products-grid">
	@foreach($products as $product)
		<div class="product">
			<?php
				$img = $product->getImagePath(0);
				if ($img == null) {
					$img = '';
				}

				$link = url(\Request::path() .'/' .$product->slug);
			?>
			<div class="preview">
				<a href="{{$link}}" style="background-image: url('{{$img}}');" class="image">
				</a>
				<button data-product-id="{{{$product->id}}}" class="button large primary quick-peek end"><i class="fa fa-search"></i> @lang('Quick Peek')</button>
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