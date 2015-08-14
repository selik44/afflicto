@extends('admin.layout')

@section('title')
    @lang('admin.variants') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.variants')</h3>
    {!! $filters !!}
@stop

@section('content')
    {!! $table !!}
@stop

@section('footer')
    {!! $pagination !!}
@stop