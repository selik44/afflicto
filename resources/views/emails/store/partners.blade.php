@extends('emails.master')

@section('header')
	<h2>Samarbeids-forespørsel fra {{$input['email']}}</h2>
@stop

@section('content')
	<table class="table">
		<tbody>
		<tr>
			<th>Organisasjon</th>
			<td>{{$input['who']}}</td>
		</tr>

		<tr>
			<th>Navn</th>
			<td>{{$input['name']}}</td>
		</tr>

		<tr>
			<th>Telefon</th>
			<td>{{$input['phone']}}</td>
		</tr>

		<tr>
			<th>E-mail Addresse</th>
			<td>{{$input['email']}}</td>
		</tr>

		<tr>
			<th>Om</th>
			<td>{{$input['about']}}</td>
		</tr>

		@if(isset($input['website']))
			<tr>
				<th>Webside</th>
				<td>{{$input['website']}}</td>
			</tr>
		@endif

		@if(isset($input['instagram']))
			<tr>
				<th>Instagram</th>
				<td>{{$input['instagram']}}</td>
			</tr>
		@endif
		</tbody>
	</table>
@stop

@section('footer')
@stop