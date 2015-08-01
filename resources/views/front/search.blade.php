@extends('front.layout')

@section('title')
	Search - @parent
@stop

@section('breadcrumbs')
	{!! Breadcrumbs::render('search') !!}
@stop

@section('article')
	<div class="search-results paper" style="padding: 1rem">
        <h1>@lang('store.search')</h1>
        <p class="lead">Search Results For "{{Input::get('terms')}}"</p>

        @if(count($products) == 0 && count($categories) == 0)
        <p class="lead">
            @lang('store.search no results')
        </p>
        @endif
    </div>

    @if(count($products) > 0 || count($categories) > 0)
        @if(count($categories) > 0)
            <div class="paper" style="padding: 1rem;">
                <h4>Categories</h4>
                <ul class="flat end"></ul>
                @foreach($categories as $cat)
                    <li><a href="{{$cat->getPath()}}">{{$cat->name}}</a></li>
                @endforeach
            </div>
        @endif

        @if(count($products) > 0)
            @include('front.partial.products-grid', ['products' => $products, 'withMenu' => false])
        @endif
    @endif
@stop