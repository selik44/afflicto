@extends('admin.layout')

@section('title')
	Products - @parent
@stop

@section('page')
    <div class="row end">
        <h3 class="pull-left end" style="margin-right: 2rem">Products</h3>
        <div class="pull-left">{!! $filters !!}</div>
    </div>
    <hr class="small">

    {!! $table !!}
    <hr class="end">
    {!! $pagination !!}

    <hr>

    <div id="tableActions">
        <button disabled class="large move"><i class="fa fa-move"></i> Move</button>
    </div>
@stop