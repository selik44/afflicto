@extends('admin.layout')

@section('title')
    @lang('admin.users') - @parent
@stop

@section('header')
    <h3 class="title">@lang('Reviews')</h3>
    {{--{!! $filters !!}--}}
@stop

@section('content')
    {!! $filters !!}
    {!! $table !!}
@stop

{{--@section('footer')--}}
    {{--{!! $pagination !!}--}}
{{--@stop--}}