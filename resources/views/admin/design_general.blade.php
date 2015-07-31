@extends('admin.layout')

@section('title')
    Design - @parent
@stop

@section('page')
    <h2 class="end">Design</h2>
    <hr>

    {!! Former::open_for_files()
        ->action(route('admin.design.save'))
        ->method('PUT')
     !!}

    {!! Former::file('background') !!}

    {!! Former::submit('save')->class('large success') !!}

    {!! Former::close() !!}
@stop