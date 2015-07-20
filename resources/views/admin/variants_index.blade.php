@extends('admin.layout')

@section('title')
    @lang('admin.variants') - @parent
@stop

@section('page')
    <h2 class="end">@lang('admin.variants')</h2>
    <hr/>

    {!! $filters !!}

    {!! $table !!}
    <br>
    {!! $pagination !!}
@stop