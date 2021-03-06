@extends('admin.layout')

@section('title')
    @lang('admin.edit') - @lang('admin.manufacturers') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.manufacturers') <small>{{$manufacturer->name}}</small></h3>
@stop

@section('content')
    {!! $form->open
    ->action(route('admin.manufacturers.update', $manufacturer))
    ->method('PUT')
    !!}

    {!! $form->name !!}

    {!! $form->slug !!}

    {!! $form->description->addClass('wysiwyg') !!}

    <label for="image">Image</label>
    @if($manufacturer->image)
        <img src="{{asset('images/manufacturers/' .$manufacturer->image->name)}}" alt="{{$manufacturer->name}} Logo">
    @endif
    {!! $form->image->label(null) !!}

	{!! $form->prepurchase_enabled !!}
			
	{!! $form->prepurchase_days !!}
@stop

@section('footer')
    {!! Former::submit('save')->class('large success') !!}
    {!! Former::close() !!}
@stop

@section('scripts')
    @parent

     <!-- CK EDITOR -->
    <script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>

    <script>
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

        var form = $("form");

        form.on('submit', function(e) {
            $('.wysiwyg').each(function() {
                var html = $(this).parent().find('div[contenteditable="true"]').html();
                $(this).html(html);
            });
        });
    </script>
@stop