<!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">

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
<?php
    $route = Request::route();
    if (is_object($route)) {
        $route = $route->getName();
    }else {
        $route = '';
    }
?>
<body id="route-{{Request::route()->getName()}}">
	
	@yield('body')

	@section('scripts')
		<script src="{{ asset('/js/all.js') }}"></script>
	@show

</body>
</html>