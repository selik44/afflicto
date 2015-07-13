<!DOCTYPE html>
<html>
<head>
	<title>
		{{$title}}
	</title>

    <style>
        <?php
            echo file_get_contents(base_path('public/css/lib.css'));
            echo file_get_contents(base_path('public/css/friluft.css'));
        ?>
    </style>
</head>
<body>
	<div class="module clearfix ninesixty">
		<div class="module-header">
			@section('header')
				@if(isset($header))
					{{$header}}
				@endif
			@show
		</div>
		<div class="module-content">
			@section('content')
				@if(isset($content))
					{{$content}}
				@endif
			@show
		</div>
		<div class="module-footer">
			<p class="small"><a href="{{url()}}">123Friluft</a></p><br>
		</div>
	</div>	
</body>
</html>