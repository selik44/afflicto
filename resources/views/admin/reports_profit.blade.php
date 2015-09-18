@extends('admin.layout')

@section('title')
	Profit - Rapporter - @parent
@stop

@section('header')
	<h3 class="title">Rapporter - Profit</h3>
	<form action="{{route('admin.reports.profit')}}" class="inline pull-right">
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
				<th>Ordrenummer</th>
				<th>Dato</th>
				<th>Profit</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="3"><h4 class="pull-right">Totalt: <strong>{{$profit}},-</strong></h4></th>
			</tr>
		</tfoot>
		<tbody>
			@foreach($orders as $order)
				<tr>
					<td>{{$order->id}}</td>
					<td>{{$order->created_at->format('d F Y H:i')}}</td>
					<td>{{$order->getProfit()}},-</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@stop

@section('footer')
@stop