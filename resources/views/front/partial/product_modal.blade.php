<div class="product clearfix" id="product-modal-{{$product->id}}">
	<div class="col-sm-7 tight-left product-images">
		<div class="slider">
			<div class="container">
				@foreach($product->images as $image)
					<div class="slide" style="background-image: url('{{$product->getImagePath($image)}}');">
					</div>
				@endforeach
			</div>
		</div>
	</div>
	
	<hr class="visible-xs">

	<div class="col-sm-5 tight-right product-info">
		<h2 class="product-name end">
			<a href="{{url($product->getPath())}}">{{{$product->name}}}</a>
		</h2>
		
		<table class="table bordered product-data">
				@if(mb_strlen($product->brand) > 0)
					<tr class="data-brand">
						<th>Brand:</th>
						<td>{{{$product->brand}}}</td>
					</tr>
				@endif
				
				@if($product->weight > 0)
					<tr class="data-weight">
						<th>Weight:</th>
						<td>{!!$product->getFormattedWeight()!!}</td>
					</tr>
				@endif
		</table>
		
		<hr class="small">

		<p class="lead product-summary">
			{!!$product->summary!!}
		</p>

		<div class="product-controls">
			<form action="{{url('cart')}}" method="POST">
				<input type="hidden" name="_method" value="put">
				<input type="hidden" name="_token" value="{{csrf_token()}}">
				
				<input type="hidden" name="id" value="{{$product->id}}">
				
				<input type="submit" value="Buy" class="success large">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var product = $("#product-modal-{{$product->id}}");
		product.find('.slider').friluftSlider();
	});
</script>