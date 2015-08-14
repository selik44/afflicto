@extends('admin.layout')

@section('title')
    @lang('admin.create') - @lang('admin.users') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.users') <small>@lang('admin.create')</small></h3>
@stop

@section('content')
    {!! $form->open !!}

    <div class="row right">
        <div class="col-xs-6 tight-left">
            {!! $form->firstname !!}
        </div>

        <div class="col-xs-6 tight-right">
            {!! $form->lastname !!}
        </div>
    </div>

    <div class="row">
        {!! $form->email !!}

        {!! $form->role_id !!}
    </div>
@stop

@section('footer')
    {!! Former::submit('Create')->class('large primary') !!}
    {!! Former::close() !!}
@stop