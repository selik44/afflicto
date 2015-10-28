<!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

    @section('meta_description')
    @show

    @section('meta_keywords')
    @show

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
        $route = str_replace('.', '-', $route->getName());
    }else {
        $route = '';
    }
?>
<body id="route-{{$route}}">
	
	@yield('body')

	@section('scripts')
		<script src="{{ asset('/js/all.js') }}"></script>
		<script>
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': Friluft.token,
				}
			});
		</script>
	@show

</body>
</html>