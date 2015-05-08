@extends('admin.layout')

@section('title')
	Edit Product - @parent
@stop

@section('page')
	<h2>Edit Product</h2>
	<form class="vertical" action="{{route('admin.products.update', $product)}}" method="POST">
		<input type="hidden" name="_token" value="{{csrf_token()}}">
		<input type="hidden" name="_method" value="PUT">
		<div class="row">
			<div class="col-m-6">
				<label for="name">Name <span class="color-error">*</span>
					<input type="text" name="name" value="{{$product->name}}" required maxlength="255">
				</label>
			
				<label for="slug">Slug <span class="color-error">*</span>
					<input type="text" name="slug" value="{{$product->slug}}" required maxlength="255">
					<p class="small muted">URL friendly name</p>
				</label>

				<label for="brand">Brand 
					<input type="text" name="brand"value="{{$product->slug}}" maxlength="255">
				</label>

				<label for="model">Model
					<input type="text" name="model" value="{{$product->slug}}" maxlength="255">
				</label>

				<label for="summary">Summary
					<textarea name="summary" rows="4">{{$product->summary}}</textarea>
				</label>

				<label for="stock">Stock
					<input type="number" name="stock" value="{{$product->stock}}">
					<small class="muted">How many are in stock?</small>
				</label>
				
				<label for="categories">Categories
					<select name="categories[]" id="categories-select" multiple>
						<option value=""></option>
						@foreach($categories as $category)
							@if($product->categories->contains($category))
								<option selected value="{{$category->id}}">{{$category->name}}</option>
							@else
								<option value="{{$category->id}}">{{$category->name}}</option>
							@endif
						@endforeach
					</select>
				</label>

				<label class="checkbox-container end" for="enabled">Enabled
					<div class="checkbox">
						@if($product->enabled)
							<input checked type="checkbox" id="enabled" name="enabled">
						@else
							<input type="checkbox" id="enabled" name="enabled">
						@endif
						<span></span>
					</div>
				</label>

			</div>
	
			<hr class="hidden-m-up">

			<div class="col-m-6">
				<h4>Product Info</h4>
				
				<label for="weight">Weight
					<input type="number" name="weight" value="0">
				</label>
					
				<label for="in-price">In price
					<input type="number" name="in_price" value="0">
				</label>

				<label for="price">Price
					<input type="number" name="price" value="0">
				</label>

				<label for="tax">Tax Percentage
					<input type="number" name="tax_percentage" value="25">
				</label>

				<h4>Attributes</h4>
				<hr>
			</div>
		</div>
		
		<hr>

		<div class="row">
			<div class="col-xs-12">
				<h4>Description</h4>
				<label for="description">Description
				<textarea rows="15" name="description">{{$product->description}}</textarea>
				</label>
			</div>
		</div>

		<div class="footer-height-fix"></div>

		<footer id="footer">
			<div class="inner">
				<input type="submit" value="Save" class="primary end">
			</div>
		</footer>
	</form>
@stop

@section('scripts')
	@parent
	<script type="text/javascript">
		var form = $("form");
		
		form.find('#categories-select').chosen();

		form.find('[name="slug"]').autoSlug({other: '[name="name"]'});
	</script>
@stop