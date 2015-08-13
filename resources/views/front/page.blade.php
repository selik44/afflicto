@extends('front.layout')

@section('breadcrumbs', Breadcrumbs::render('page', $page))

@section('article')
    <h2 class="end">{{$page->title}}</h2>
    <div style="padding: 3rem 0;">
        {!! $content !!}
    </div>
@stop