@extends('admin.layout')

@section('title')
    @lang('admin.roles') - @parent
@stop

@section('page')
    <h2>@lang('admin.roles')</h2>
    {!! $table !!}

    <div class="footer-height-fix"></div>
    <footer id="footer">
        <div class="inner">
            {!! $pagination !!}
        </div>
    </footer>
@stop