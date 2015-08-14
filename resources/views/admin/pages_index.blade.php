@extends('admin.layout')

@section('title')
    @lang('admin.pages') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.page')</h3>
@stop

@section('content')
    {!! $table !!}
@stop

@section('footer')
    {!! $pagination !!}
@stop