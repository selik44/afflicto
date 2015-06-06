@extends('admin.layout')

@section('title')
    Users - @parent
@stop

@section('page')
    <h2 class="end">@lang('admin.users') <small>@lang('admin.new') @lang('admin.user')</small></h2>

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

    {!! Former::submit('Create')->class('large primary') !!}
    {!! Former::close() !!}
@stop