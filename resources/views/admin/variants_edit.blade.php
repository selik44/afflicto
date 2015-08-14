@extends('admin.layout')

@section('title')
    @lang('admin.edit') - @lang('admin.variants') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.variants') - @lang('admin.edit')</h3>
@stop

@section('content')
    {!! Former::open()
    ->action(route('admin.variants.update', ['variant' => $variant]))
    ->method('PUT')
    !!}

    {!! Former::text('admin_name') !!}

    {!! Former::text('name') !!}

    {!! Former::checkbox('filterable')->value(1) !!}

    <div class="values-container module">
        <div class="module-header">
            <h6>Values</h6>
        </div>
        <div class="module-content clearfix">
            <div class="values flat">
                @foreach($variant->data['values'] as $value)
                    <div class="row" style="margin-bottom: 1rem;"><input type="text" name="variant-{{$value['id']}}" value="{{$value['name']}}" class="pull-left"> <a
                                href="#" class="pull-left button small remove"><i class="fa fa-trash"></i></a></div>
                @endforeach
            </div>
        </div>
        <div class="module-footer">
            <a href="#" class="button add"><i class="fa fa-plus"></i> Add Value</a>
        </div>
    </div>
@stop

@section('footer')
    {!! Former::submit(trans('admin.save'))->class('large primary') !!}
    {!! Former::close() !!}
@stop

@section('scripts')
    @parent

    <script>
        var form = $("form");
        var data = form.find('input[name="data"]');

        var container = form.find('.values-container');
        var values = container.find('.module-content .values');

        container.find('.module-footer .add').click(function(e) {
            e.preventDefault();
            values.append('<div class="row" style="margin-bottom: 1rem;"><input type="text" name="values[]" class="pull-left"> <a href="#" class="pull-left button error remove"><i class="fa fa-trash"></i></a></div>');
        });

        values.on('click', 'div .remove', function() {
            $(this).parent().remove();
        });
    </script>
@stop