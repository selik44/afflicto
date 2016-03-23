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