@extends('admin.layout')

@section('title')
    @lang('admin.manufacturers') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.manufacturers')</h3>
@stop

@section('page')
    {!! $table !!}
@stop

@section('footer')
    {!! $pagination !!}
@stop