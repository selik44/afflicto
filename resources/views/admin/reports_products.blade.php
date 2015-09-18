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

		<input type="submit" value="Hent" class="success">
	</form>
@stop

@section('content')
	<table class="striped bordered">
		<thead>
		<tr>
			<th>ID</th>
			<th>Navn</th>
			<th>Salg</th>
		</tr>
		</thead>

		<tbody>
		@foreach($products as $product)
			<tr>
				<td>{{$product->id}}</td>
				<td>{{$product->name}}</td>
				<td>{{$product->sales}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@stop

@section('footer')
@stop