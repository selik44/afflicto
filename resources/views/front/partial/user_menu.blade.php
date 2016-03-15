<?php
    $class = ['nav'];
    if (!isset($horizontal) || ! $horizontal) {
        $class[] = 'vertical fancy';
    }else {
        $class[] = 'hidden-l-up';
    }
?>

<ul class="{{implode(' ', $class)}}">
    <li><a href="{{route('user')}}">@lang('store.user.my account')</a></li>
    <li><a href="{{route('user.settings')}}">@lang('store.user.settings')</a></li>
</ul>