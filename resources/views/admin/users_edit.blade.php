@extends('admin.layout')

@section('title')
    @lang('admin.edit') - @lang('admin.users') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.users') <small>{{$user->name}}</small></h3>
@stop

@section('content')
    {!! $form->open
        ->action(route('admin.users.update', ['user' => $user->id]))
        ->method('PUT')
    !!}

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
    {!! Former::submit('Lagre')->class('large success') !!}
    {!! Former::close() !!}
@stop