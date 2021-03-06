@extends('admin.layout')

@section('title')
    @lang('admin.new') @lang('admin.page') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.pages') <small>@lang('admin.new')</small></h3>
@stop

@section('content')
    {!! Former::open()->method('POST')->action(route('admin.pages.store'))->class('vertical') !!}

    {!! Former::text('title') !!}
    {!! Former::text('slug') !!}

    {!! Former::textarea('content')->class('wysiwyg') !!}

    {!! Former::checkbox('sidebar') !!}
@stop

@section('footer')
    {!! Former::submit('Save')->class('large') !!}
    {!! Former::close() !!}
@stop

@section('scripts')
    @parent

    <!-- CK EDITOR -->
    <script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>

    <script>
        var form = $("form");

        //autoslug the title
        form.find('[name="slug"]').autoSlug({other: '[name="title"]'});

        //init ckeditor
        CKEDITOR.stylesSet.add('friluft', [
            //blocks
            {name: 'Heading 3', element: 'h3'},
            {name: 'Heading 4', element: 'h4'},
            {name: 'Heading 5', element: 'h5'},
            {name: 'Heading 6', element: 'h6'},
            {name: 'Lead Paragraph', element: 'p', attributes: {'class': 'lead'}},
            {name: 'Paragraph', element: 'p'},
            {name: 'Small Paragraph', element: 'p', attributes: {'class': 'small'}},
            {name: 'Muted Paragraph', element: 'p', attributes: {'class': 'muted'}},
            {name: 'Small & Muted Paragraph', element: 'p', attributes: {'class': 'muted small'}},
            {name: 'Blockquote', element: 'blockquote'},
        ]);

        $('.wysiwyg').each(function() {
            CKEDITOR.inline(this, {
                language: '{{\App::getLocale()}}',
                contentsCss: '{{asset('css/friluft.css')}}',
                stylesSet: 'friluft',
            });
        });

        form.on('submit', function(e) {
            $(".wysiwyg").each(function() {
                var html = $(this).parent().find('div[contenteditable="true"]').html();
                $(this).html(html);
            });
        });
    </script>
@stop