@extends('admin.layout')

@section('title')
	New Product - @parent
@stop

@section('page')
	<h2>New Product</h2>
	<form class="vertical" action="{{url('admin/products')}}" method="POST">
		<input type="hidden" name="_token" value="{{csrf_token()}}">

		<div class="row">
			<div class="col-m-6">
				<label for="name">Name <span class="color-error">*</span>
					<input type="text" name="name" required maxlength="255">
				</label>
			
				<label for="slug">Slug <span class="color-error">*</span>
					<input type="text" name="slug" required maxlength="255">
					<p class="small muted">URL friendly name</p>
				</label>

				<label for="brand">Brand 
					<input type="text" name="brand" maxlength="255">
				</label>

				<label for="model">Model
					<input type="text" name="model" maxlength="255">
				</label>

				<label for="description">Description
					<textarea name="description" rows="4"></textarea>
				</label>

				<label for="stock">Stock
					<input type="number" name="stock" value="0">
					<small class="muted">How many are in stock?</small>
				</label>
				
				<label for="categories">Categories
					<select name="categories[]" id="categories-select" multiple>
						<option value=""></option>
						@foreach($categories as $category)
							<option value="{{$category->id}}">{{$category->name}}</option>
						@endforeach
					</select>
				</label>

				<label class="checkbox-container end" for="enabled">Enabled
					<div class="checkbox">
						<input type="checkbox" id="enabled" name="enabled">
						<span></span>
					</div>
				</label>

				<hr>

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
			</div>
			
			<hr class="hidden-m-up">

			<div class="col-m-6">
				<h4>Attributes</h4>
			</div>
		</div>

		<div class="footer-height-fix"></div>

		<footer id="footer">
			<div class="inner">
				<input type="submit" name="create" value="Create" class="primary end">
				<input type="submit" name="continue" value="Create & Continue" class="secondary end">
				<input type="reset" value="reset" class="end">
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