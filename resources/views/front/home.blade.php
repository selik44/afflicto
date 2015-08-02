@extends('front.layout')

@section('breadcrumbs')
@stop

@section('slider')
    <div class="left">
        <div class="slider">
            <div class="container">
                @foreach(\Friluft\Image::whereType('slideshow')->orderBy('order', 'asc')->get() as $slide)
                    <a href="{{$slide->data['link']}}" style="background-image: url('{{asset('/images/' .$slide->name)}}');" class="slide">
                        @if($slide->data)
                            <div class="elements" style="padding: 1rem">
                                @foreach($slide->data['elements'] as $el)
                                    <div class="element"
                                         data-start="{!! $el['start'] !!}"
                                         data-end="{!! $el['end'] !!}"
                                         data-offset-x="{!! $el['offsetX'] !!}"
                                         data-offset-y="{!! $el['offsetY'] !!}"
                                         data-delay="{!! $el['delay'] !!}"
                                         data-type="{!! $el['type'] !!}"
                                         data-speed="{!! $el['speed'] !!}"
                                    >
                                        {!! $el['content'] !!}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="right">
        @if(isset($images['top_right']))
            <a href="{{$images['top_right']->data['link']}}" class="image" style="background-image: url('{{asset('images/' .$images['top_right']->name)}}');">
            </a>
        @endif

        @if(isset($images['bottom_right']))
            <a href="{{$images['bottom_right']->data['link']}}" class="image" style="background-image: url('{{asset('images/' .$images['bottom_right']->name)}}');">
            </a>
        @endif
    </div>
@stop


@section('scripts')
	@parent

	<script>
		$("#slider .slider").friluftSlider({
			delay: 4000,
			transitionSpeed: 600,
			autoHeight: true,
			heightRatio: 7 / 16,
            useElements: true,
		});
	</script>
@stop


@section('article')
	<div class="products-popular paper clearfix" style="padding: 2rem;">
        <h2 class="end">@lang('store.popular products')</h2>

        <div class="row">
		@foreach($popular as $product)
			<div class="col-xs-6 col-l-3">
				@include('front.partial.products-block', ['product' => $product])
			</div>
		@endforeach
        </div>
	</div>

    <br>

	<div class="row paper">
		<div class="col-xs-6 tight">
			<a href="#">
				<img src="http://lorempixel.com/800/600/abstract"/>
			</a>
		</div>

		<div class="col-xs-6 tight">
			<a href="#">
				<img src="http://lorempixel.com/800/600/sports"/>
			</a>
		</div>
	</div>
@stop