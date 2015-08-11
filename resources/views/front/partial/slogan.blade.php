<?php
    $route = app('router')->getCurrentRoute()->getName();

    $slogan = Friluft\Setting::whereMachine('slogan_content')->first()->value;
    $slogan1 = Friluft\Setting::whereMachine('store_slogan_1_content')->first()->value;
    $slogan2 = Friluft\Setting::whereMachine('store_slogan_2_content')->first()->value;
    $slogan3 = Friluft\Setting::whereMachine('store_slogan_3_content')->first()->value;
    $slogan4 = Friluft\Setting::whereMachine('store_slogan_4_content')->first()->value;
    $slogan_bg = Friluft\Setting::whereMachine('slogan_background')->first();
    $slogan_color = Friluft\Setting::whereMachine('slogan_color')->first();
?>

<div id="slogan" class="clearfix {{$route}}" style="background-color: {{$slogan_bg->value}}; color: {{$slogan_color->value}};">
    <div class="inner clearfix">
        @if($route == 'home')
            <div class="col-xs-12">{{$slogan}}</div>
        @else
            <div class="col-xs-3"><i class="fa fa-check"></i> {{$slogan1}}</div>
            <div class="col-xs-3"><i class="fa fa-check"></i> {{$slogan2}}</div>
            <div class="col-xs-3"><i class="fa fa-check"></i> {{$slogan3}}</div>
            <div class="col-xs-3"><i class="fa fa-check"></i> {{$slogan4}}</div>
        @endif
    </div>
</div>