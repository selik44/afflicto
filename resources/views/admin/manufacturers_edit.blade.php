@extends('admin.layout')

@section('title')
    @lang('admin.edit') - @lang('admin.manufacturers') - @parent
@stop

@section('page')
    <h2 class="end">@lang('admin.manufacturers') <span class="muted">{{$manufacturer->name}}</span></h2>
    <hr/>

    {!! $form->open
    ->action(route('admin.manufacturers.update', $manufacturer))
    ->method('PUT')
    !!}

    {!! $form->name !!}

    {!! $form->slug !!}

    {!! Former::submit(trans('admin.save'))->class('large success') !!}
    {!! Former::close() !!}
@stop