@extends('admin.layout')

@section('title')
    @lang('admin.new') @lang('admin.tag') - @parent
@stop

@section('page')
    <h2 class="end">New Tag</h2>
    <hr>

    <form class="vertical" action="{{route('admin.tags.update', $tag)}}" method="POST">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <input type="hidden" name="_method" value="PUT">
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

        <hr>

        <input type="submit" class="large success" name="save" value="Save">

    </form>
@stop

@section('scripts')
    @parent
    <script type="text/javascript">
        $('#icon-selector').fontIconPicker();
    </script>
@stop