@extends('front.layout')

@section('breadcrumbs', Breadcrumbs::render('page', $page))

@section('article')
    <h2 class="end">{{$page->title}}</h2>

    {!! $page->content !!}
@stop