@extends('admin.layout')

@section('title')
    @lang('admin.users') - @parent
@stop

@section('header')
    <h3 class="title">@lang('Reviews')</h3>
    {{--{!! $filters !!}--}}
@stop

@section('content')
    {!! Form::model($review, ['method' => 'patch']) !!}

    {!! Form::number('rating') !!}
    {!! Form::textarea('comment') !!}
    {!! Form::select('approved', [trans('admin.reviews.edit.unprocessed'), trans('admin.reviews.edit.processed')], $review->approved) !!}
    {!! Form::submit(trans('admin.reviews.edit.update')) !!}
    <a href="{!! URL::previous() !!}" class="btn btn-default">{{ trans('admin.reviews.edit.cancel') }}</a>
    {!! Form::close() !!}
@stop