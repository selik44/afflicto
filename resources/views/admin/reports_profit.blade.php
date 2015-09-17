@extends('admin.layout')

@section('title')
	Profit - Rapporter - @parent
@stop

@section('header')
	<h3 class="title">Rapporter - Profit</h3>
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
				<th>Totalt: <strong>{{$profit}}</strong></th>
			</tr>
		</tfoot>
		<tbody>
			@foreach($orders as $order)
				<tr>
					<td>{{$order->id}}</td>
					<td>{{$order->created_at->format('d F Y H:i')}}</td>
					<td>{{$order->getProfit()}}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@stop