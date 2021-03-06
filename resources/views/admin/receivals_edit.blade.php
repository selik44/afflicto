@extends('admin.layout')

@section('title')
	@lang('admin.edit receival') - @parent
@stop

@section('header')
	<h3 class="title">
		@if($receival->rest)
			Rest-varebestilling
		@else
			Varebestilling
		@endif
		#{{$receival->id}} for {{$receival->manufacturer->name}}
	</h3>
@stop

@section('content')
	<div id="settings">
		<label for="expected_arrival">Forventet Ankomst
			<input type="date" class="expected-arrival" value="{{$receival->expected_arrival->format('Y-m-d')}}">
		</label>
	</div>

	<hr>

	<div id="add-form">
		<h4>@lang('admin.add')</h4>
		<div class="row">
			<div class="col-xs-9 product">
				<?php
					$empty = new stdClass();
					$empty->id = -1;
					$empty->name = '------';
					$products->prepend($empty);
				?>
				{!! Former::select('product')->fromQuery($products, 'name', 'id') !!}
			</div>
			<div class="col-xs-3">
				<button class="add">@lang('admin.add')</button>
			</div>
		</div>
	</div>
	<hr>

	<h4>@lang('admin.products')</h4>
	<table id="products-table" class="bordered">
		@foreach($receival->products as $product)
			<?php
				$model = \Friluft\Product::withTrashed()->find($product['id']);
			?>
			@if($model)
				@include('admin.partial.receivals_line', ['receival' => $receival, 'product' => $model, 'current' => $product])
			@endif
		@endforeach
	</table>

	<hr>

	{!! Former::submit('save')->class('success save-button') !!}
@stop

@section('scripts')
	@parent

	<script>
		var receivalID = {{$receival->id}};
		var addForm = $("#add-form");
		var productsTable = $("#products-table");
		var addButton = addForm.find('button.add');
		var saveButton = $(".save-button");

		//add
		addButton.click(function() {
			var productID = parseInt(addForm.find('.product select').val());
			if (productID == -1) {
				return false;
			}

			var exists = false;
			if (productsTable.find('tr.product[data-id="' + productID + '"]').length >= 1) {
				console.log('already added!');
				return false;
			}

			$.get(Friluft.URL + '/admin/receivals/line/' + receivalID + '/' + productID, function(html) {
				productsTable.append(html);
			});
		});

		//remove
		productsTable.on('click', 'tr.product .controls button.remove', function() {
			$(this).parents('tr.product').remove();
		});

		//save
		saveButton.click(function() {
			//create the products JSON object.
			var products = [];

			//loop through all the products
			productsTable.find('tr.product').each(function() {
				var product = {
					id: parseInt($(this).attr('data-id')),
					variants: $(this).hasClass('variants'),
				};

				//variants?
				if ($(this).hasClass('variants')) {
					product.order = {};

					//add all the variants
					$(this).find('td.options tr.variant').each(function() {
						var id = $(this).attr('data-stock-id');
						var quantity = parseInt($(this).find('td.quantity input').val());
						product.order[id] = quantity;
					});
				}else {
					//order some amount of this non-variant product
					product.order = parseInt($(this).find('.options td.quantity input').val());
				}

				products.push(product);
			});

			//send PUT request
			console.log('Saving: ');
			console.log(products);

			$.post(Friluft.URL + '/admin/receivals/' + receivalID, {
				_method: 'PUT',
				_token: Friluft.token,
				products: products,
				expected_arrival: $("#settings input.expected-arrival").val(),
			}, function(response) {
				console.log('Response:');
				console.log(response);
			});
		});
	</script>
@stop