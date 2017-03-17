<?php
        //get store but fallback to 'friluft' if for some reason it's missing.
        $store = \Friluft\Store::current();
        if ($store) {
            $storeMachine = $store->machine;
            $storeName = $store->name;
        }else {
            $storeMachine = 'friluft';
            $storeName = '123 Friluft';
        }
?>
<!DOCTYPE html>
<html lang="{{App::getLocale()}}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <style type="text/css">
        <?php
            echo file_get_contents(public_path('css/lib.css'));
            echo file_get_contents(public_path('css/' .$storeMachine .'.css'));
        ?>
    </style>
</head>
<body>
    <div class="text-center tower">
        <a href="{{url()}}" class="logo">
            <img style="max-width: 250px;" src="{{url('images/' .$storeMachine .'.png')}}">
        </a>
    </div>
	<div class="module clearfix ninesixty">
		<div class="module-content">
			@section('content')
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A accusantium adipisci, aperiam at aut blanditiis distinctio dolor et facilis fuga ipsum laborum nam, natus necessitatibus porro possimus quam quidem sit!</p>
			@show
		</div>
		<div class="module-footer" style="padding: 1rem">
            @section('footer')

            @show
		</div>
	</div>
    <hr>
    <p class="small text-center">
        {{$storeName}}
    </p>
</body>
</html>