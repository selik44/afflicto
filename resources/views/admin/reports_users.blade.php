@extends('admin.layout')

@section('title')
	Nye kontoer - Rapporter - @parent
@stop

@section('header')
	<h3 class="title">Rapporter - Nye Kontoer</h3>
	<form action="{{route('admin.reports.users')}}" class="inline pull-right">
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
			<th>Kundenummer</th>
			<th>Dato</th>
			<th>Navn</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th colspan="3"><h4 class="pull-right">Totalt: <strong>{{count($users)}}</strong></h4></th>
		</tr>
		</tfoot>
		<tbody>
		@foreach($users as $user)
			<tr>
				<td>{{$user->id}}</td>
				<td>{{$user->created_at->format('d F Y H:i')}}</td>
				<td>{{$user->name}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@stop