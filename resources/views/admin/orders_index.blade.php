@extends('admin.layout')

@section('title')
    Orders - @parent
@stop

@section('page')
    <h2>Orders</h2>
    {!! $table !!}

    <div class="footer-height-fix"></div>
    <footer id="footer">
        <div class="inner">
            {!! $pagination !!}
        </div>
    </footer>
@stop