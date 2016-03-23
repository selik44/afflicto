@extends('admin.layout')

@section('title')
    @lang('admin.roles') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.roles')</h3>
@stop

@section('content')
    {!! $table !!}
@stop

@section('footer')
    {!! $pagination !!}
@stop