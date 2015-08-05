@extends('admin.layout')

@section('title')
    @lang('admin.create') - @lang('admin.variants') - @parent
@stop

@section('page')
    <h2 class="end">@lang('admin.variants') <small>@lang('admin.create')</small></h2>
    <hr/>

    {!! Former::open()
    ->action(route('admin.variants.store'))
    ->method('POST')
    !!}

    {!! Former::text('admin_name') !!}

    {!! Former::text('name') !!}

    {!! Former::checkbox('filterable') !!}

    {!! Former::submit(trans('admin.create'))->class('large primary') !!}
    {!! Former::close() !!}
@stop