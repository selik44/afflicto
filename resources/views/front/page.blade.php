@extends('front.layout')

@section('breadcrumbs', Breadcrumbs::render('page', $page))

@section('article')
    <div style="padding: 3rem 0;">
	    @if($page->slug == 'konkurranser')
			<div class="row right contests-view">
				<div class="tight-left left">
					<div class="grid paper col-xl-3 visible-xl">
						<?php $left = \Friluft\Tag::whereType('contest_left')->first(); ?>
						@foreach($left->products()->where('enabled', '=', 1)->orderByRaw('RAND()')->get() as $product)
							@include('front.partial.products-block', ['product' => $product, 'withBuyButton' => true])
						@endforeach
					</div>
				</div>

				<div class="middle paper col-xl-6 col-l-8">
					<h2 class="end">{{$page->title}}</h2>
					<hr style="margin-top: 0.5rem">
					{!! $content !!}
				</div>

				<div class="tight-right right col-xl-3 col-l-4">
					<div class="grid paper">
						<?php $left = \Friluft\Tag::whereType('contest_right')->first(); ?>
						@foreach($left->products()->where('enabled', '=', 1)->orderByRaw('RAND()')->get() as $product)
							@include('front.partial.products-block', ['product' => $product, 'withBuyButton' => true])
						@endforeach
					</div>
				</div>
			</div>
		@else
		    {!! $content !!}
		@endif
    </div>
@stop