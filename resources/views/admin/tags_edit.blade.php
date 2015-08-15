@extends('admin.layout')

@section('title')
    @lang('admin.edit') @lang('admin.tag') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.tags') <small>@lang('admin.edit')</small></h3>
@stop

@section('content')
    {!! Former::open()
        ->method('PUT')
        ->action(route('admin.tags.update', $tag))
        ->class('vertical')
     !!}
        <label for="label">Label <span class="color-error">*</span>
            <input type="text" name="label" maxlength="255" required value="{{$tag->label}}">
        </label>

        <label for="icon">Icon
            <select id="icon-selector" name="icon">
                <option value="">None</option>
                @foreach($icons as $icon)
                    @if($tag->icon == 'fa fa-' .$icon)
                        <option selected="selected" value="fa fa-{{$icon}}">fa fa-{{$icon}}</option>
                    @else
                        <option value="fa fa-{{$icon}}">fa fa-{{$icon}}</option>
                    @endif
                @endforeach
            </select>
        </label>

        <label for="color">Color
            <input type="color" name="color" value="{{$tag->color}}">
        </label>

        <label for="visible">Visible
            @if($tag->visible)
                <input type="checkbox" name="visible" checked="checked">
            @else
                <input type="checkbox" name="visible">
            @endif
        </label>

        <label for="discount">@lang('admin.discount')
            <div class="input-append">
                <input type="text" name="discount" value="{{$tag->getDiscountPercentage()}}">
                <span class="appended">%</span>
            </div>
        </label>

        <hr>

        <input type="submit" class="large success" name="save" value="Save">

    {!! Former::close() !!}
@stop

@section('scripts')
    @parent
    <script type="text/javascript">
        $('#icon-selector').fontIconPicker();
    </script>
@stop