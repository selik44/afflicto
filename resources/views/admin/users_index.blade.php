@extends('admin.layout')

@section('title')
    @lang('admin.users') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.users')</h3>
    {!! $filters !!}
@stop

@section('content')
    {!! $table !!}
@stop

@section('footer')
    {!! $pagination !!}
@stop