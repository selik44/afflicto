@extends('admin.layout')

@section('title')
    Banners - Design - @parent
@stop

@section('page')
    <div class="row end">
        <h3 class="pull-left end" style="margin-right: 2rem">Banners</h3>
        <div class="pull-left"></div>
    </div>
    <hr class="small">

    {!! Former::openForFiles()->method('PUT')->action(route('admin.banners.update')) !!}

    @foreach($images as $key => $image)
        <h4>{{ucwords(str_replace('_', ' ', $key))}}</h4>
        @if( $image)
            <img src="{{asset('images/' .$image->name)}}" class="thumbnail" width="200px">
            <br>
            {!! Former::file($key)->label(null)->value($image->name) !!}
        @else
            {!! Former::file($key)->label(null) !!}
        @endif
        <?php
            $link = '';
            $data = $image->data;
            if (is_array($data)) {
                if (isset($data['link'])) {
                    $link = $data['link'];
                }
            }
        ?>
        {!! Former::text($key .'_link')->label('link')->value($link) !!}

        <hr>
    @endforeach

    {!! Former::submit('Update')->addClass('success large') !!}

    {!! Former::close() !!}

@stop