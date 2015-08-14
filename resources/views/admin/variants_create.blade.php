@extends('admin.layout')

@section('title')
    @lang('admin.create') - @lang('admin.variants') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.variants') <small>@lang('admin.create')</small></h3>
@stop

@section('page')
    {!! Former::open()
    ->action(route('admin.variants.store'))
    ->method('POST')
    !!}

    {!! Former::text('admin_name') !!}

    {!! Former::text('name') !!}

    {!! Former::checkbox('filterable') !!}
@stop

@section('footer')
    {!! Former::submit(trans('admin.create'))->class('large primary') !!}
    {!! Former::close() !!}
@stop