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
    {!! Form::open(['route' => 'admin.reviews.bulk-update']) !!}
    {!! $table !!}
    {!! Form::select('bulk-status', [0 => trans('admin.reviews.index.unprocessed'), 1 => trans('admin.reviews.index.activate')]) !!}
    {!! Form::submit(trans('admin.reviews.index.update')) !!}
    {!! Form::close() !!}
@stop

{{--@section('footer')--}}
    {{--{!! $pagination !!}--}}
{{--@stop--}}