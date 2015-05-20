@extends('admin.layout')

@section('title')
    @lang('admin.add') - @lang('admin.manufacturers') - @parent
@stop

@section('page')
    <h2 class="end">@lang('admin.manufacturers') <span class="muted">@lang('admin.add')</span></h2>
    <hr/>

    {!! $form->open
    ->action(route('admin.manufacturers.store'))
    ->method('POST')
    !!}

    {!! $form->name !!}

    {!! $form->slug !!}

    {!! Former::submit(trans('admin.add'))->class('large primary') !!}
    {!! Former::close() !!}
@stop