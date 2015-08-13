@extends('front.layout')

@section('breadcrumbs', Breadcrumbs::render('page', $page))

@section('article')
    <div style="padding: 3rem 0;">
        <h2 class="end">{{$page->title}}</h2>
        <hr>

        {!! $content !!}
    </div>
@stop