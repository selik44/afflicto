@extends('admin.layout')

@section('title')
    Multiedit - Products - @parent
@stop

@section('page')
    <style>
        table select, table input, table textarea {
            width: 100%;
        }

        table textarea {
            min-width: 600px;
            min-height: 300px;
        }

    </style>

    {!! Former::open()->method('GET')->action(route('admin.products.quick-edit')) !!}

    <div class="row end">
        <h3 class="pull-left end" style="margin-right: 2rem">Multiedit - Products</h3>
    </div>
    <hr class="small">

    <div class="row end">
        <div class="col-xs-6">
            <h6 class="end">Filters</h6>
            {!! $filters !!}
        </div>

        <div class="col-xs-6">
            <h6 class="end">Columns</h6>
            <div class="columns clearfix">
                <?php
                    $cols = [
                        'name','slug','inprice','price','articlenumber','barcode','weight','description','summary','stock','tags','variants'
                    ];
                ?>
                @foreach($cols as $col)
                    <label class="checkbox-container" style="margin: 0.4rem; " for="column_{{$col}}" style="float: left; margin-right: 1rem;"> @lang('admin.' .$col)
                        <div class="checkbox">
                            @if(Input::has('column_' .$col))
                                <input type="checkbox" checked="checked" id="column_{{$col}}" name="column_{{$col}}">
                            @else
                                <input type="checkbox" id="column_{{$col}}" name="column_{{$col}}">
                            @endif
                            <span></span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        <br>
        {!! Former::submit('Get Columns')->addClass('primary small') !!}
        {!! Former::close() !!}
    </div>

    {!! Former::open()->method('PUT')->action(route('admin.products.quick-edit.save')) !!}
    {!! $table !!}
    <br>
    {!! $pagination !!}
    <hr>
    {!! Former::submit('Save')->addClass('success huge') !!}
    {!! Former::close() !!}
@stop

@section('scripts')
    @parent

    <!-- CK EDITOR -->
    <script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>

    <script>
        $('select.categories, select.tags, select.manufacturer, select.variants').chosen({width: '100%'});

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

        $(".wysiwyg").each(function() {
            CKEDITOR.replace(this, {
                language: '{{\App::getLocale()}}',
                contentsCss: '{{asset('css/friluft.css')}}',
                stylesSet: 'friluft',
            });
        });

    </script>
@stop