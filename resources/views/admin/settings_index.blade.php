@extends('admin.layout')

@section('title')
    @lang('admin.settings') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.settings')</h3>
@stop

@section('content')
    {!! Former::open_for_files()
        ->action(route('admin.settings.update'))
        ->method('PUT')
     !!}

    @foreach($fields as $field)
        {!! $field !!}
    @endforeach
@stop

@section('footer')
    {!! Former::submit('save')->class('large success') !!}
    {!! Former::close() !!}
@stop