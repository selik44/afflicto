@extends('admin.layout')

@section('title')
    @lang('admin.edit') @lang('admin.page') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.page') - <small>@lang('admin.edit')</small></h3>
@stop

@section('content')
    <h3 class="end">@lang('admin.edit') - @lang('admin.page')</h3>
    <hr class="small">
    {!! Former::open()->method('PUT')->action(route('admin.pages.update', $page->id))->class('vertical') !!}

    {!! Former::text('title') !!}
    {!! Former::text('slug') !!}

    {!! Former::textarea('content')->class('wysiwyg') !!}

    {!! Former::checkbox('sidebar')->value('sidebar') !!}
@stop

@section('footer')
    {!! Former::submit('Save')->class('large success') !!}
    {!! Former::close() !!}
@stop

@section('scripts')
    @parent

    <!-- CK EDITOR -->
    <script src="{{asset('js/ckeditor/ckeditor.js')}}"></script>

    <script>
        //autoslug the title
        $('form [name="slug"]').autoSlug({other: '[name="title"]'});

        CKEDITOR.replace('content');
    </script>
@stop