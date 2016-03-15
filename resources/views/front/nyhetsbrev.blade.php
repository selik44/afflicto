@extends('front.layout')

@section('title')
    Nyhetsbrev - @parent
@stop

@section('article')
    <div class=" paper" style="padding: 1rem">
        {!! Former::open()->method('POST')->action(route('nyhetsbrev.post'))->rules([
        'email' => 'required|email',
        ]) !!}

        {!! Former::email('email') !!}

        {!! Former::submit('Registrer')->class('large success') !!}

        {!! Former::close() !!}
    </div>
@stop