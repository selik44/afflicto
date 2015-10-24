@extends('admin.layout')

@section('title')
	Produkter - Rapporter - @parent
@stop

@section('header')
	<h3 class="title">Rapporter - Produkter</h3>
	<form action="{{route('admin.reports.products')}}" class="inline pull-right">
		<label for="category">Kategori
			<select name="category" id="category">
				<option value="*">Alle</option>
				@foreach(\Friluft\Category::root()->get() as $cat)
					{!! $cat->renderSelectOptions(Input::get('category')) !!}
				@endforeach
			</select>
		</label>

		<label for="from">Fra
			<input type="date" name="from" placeholder="from" value="{{$from}}">
		</label>

		<label for="to">Til
			<input type="date" name="to" placeholder="to" value="{{$to}}">
		</label>

		<input type="submit" value="Hent" class="success">
	</form>
@stop

@section('content')
	<table class="striped bordered">
		<thead>
		<tr>
			<th>ID</th>
			<th>Product</th>
			<th>Salg</th>
		</tr>
		</thead>

		<tbody>
		@foreach($products as $product)
			<?php
				$model = $product['product'];
			?>
			<tr>
				<td class="id">{{$model->id}}</td>

				<td class="product">
					@if($model->hasVariants())
						<div class="variants">
							<div class="variants-header">
								<h4><a href="#">{{$model->name}}</a></h4>
							</div>

							<div class="variants-content" style="display: none;">
								<table>
								@foreach($product['variants'] as $variant)
									<tr>
										<td><strong>{{implode(', ', $variant['string'])}}</strong></td>
										<td>{{$variant['quantity']}}</td>
									</tr>
								@endforeach
								</table>
							</div>
						</div>
					@else
						<h4>{{$model->name}}</h4>
					@endif
				</td>

				<td class="quantity">{{$product['quantity']}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@stop

@section('footer')
@stop

@section('scripts')
	@parent

	<script>
		var table = $("table");
		table.find('tr td.product .variants .variants-header a').click(function() {
			var variants = $(this).parents('.variants');
			variants.toggleClass('visible');
			variants.find('.variants-content').slideToggle();
		});
	</script>
@stop