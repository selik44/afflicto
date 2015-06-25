<!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>
		@section('title')
			{{Friluft\Store::current()->name}}.no
		@show
	</title>
	
	@section('head')
		<link href="{{ asset('/css/lib.css')}}" rel="stylesheet">
		<link href="{{ asset('/css/' .Friluft\Store::current()->machine .'.css') }}" rel="stylesheet">
	@show
	
</head>
<body>
	
	@yield('body')

	@section('scripts')
		<script src="{{ asset('/js/all.js') }}"></script>
	@show

</body>
</html>