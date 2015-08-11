@extends('admin.layout')

@section('title')
    @lang('admin.pages') - @parent
@stop

@section('page')
    <h3 class="end">@lang('admin.pages')</h3>
    <hr class="small">

    {!! $table !!}
    <hr class="end">
    {!! $pagination !!}
@stop

@section('scripts')
    @parent
    <script>

    </script>
@stop