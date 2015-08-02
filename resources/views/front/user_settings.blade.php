@extends('front.layout')

@section('title')
    @lang('store.user.settings') - @lang('store.user.my account') - @parent
@stop

@section('aside')
    <div class="block">
        @include('front.partial.user_menu')
    </div>
@stop

@section('article')
    <div class="row paper" style="padding: 1rem;">
        @include('front.partial.user_menu', ['horizontal' => true])
        <h2>@lang('store.user.settings')</h2>

        {!! Former::open()
            ->method('PUT')
            ->action(route('user.settings.save'))
            ->rules([
                'email' => 'required|email',
                'new_password' => 'min:8|confirmed:password_confirmation',
                'password_confirmation' => 'required_with:password,password_confirmation',
                'old_password' => 'required_with:password,password_confirmation',
            ])
            ->class('vertical')
         !!}

        {!! Former::email('email') !!}

        <hr>

        <h4>Endre Passord</h4>

        {!! Former::password('old_password') !!}

        {!! Former::password('password')->value('')->help('') !!}

        {!! Former::password('password_confirmation')->value('') !!}

        {!! Former::submit('save')->class('large primary') !!}

        {!! Former::close() !!}
    </div>
@stop