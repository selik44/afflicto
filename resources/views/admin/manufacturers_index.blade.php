@extends('admin.layout')

@section('title')
    @lang('admin.manufacturers') - @parent
@stop

@section('page')
    <h2>@lang('admin.manufacturers')</h2>
    {!! $table !!}

    <div class="footer-height-fix"></div>
    <footer id="footer">
        <div class="inner">
            {!! $pagination !!}
        </div>
    </footer>
@stop