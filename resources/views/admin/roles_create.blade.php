@extends('admin.layout')

@section('title')
    @lang('admin.edit') - @lang('admin.roles') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.roles') <small>@lang('admin.new')</small></h3>
@stop

@section('content')
    {!! $form->open
    ->action(route('admin.roles.store'))
    ->method('POST')
    !!}

    {!! $form->name !!}

    <h4>@lang('admin.permissions')</h4>
    <table class="permissions-table bordered striped boxed">
        <tbody>
        @foreach($permissions as $permission)
            <tr>
                <td class="input" style="width: 40px">
                    <label class="checkbox-container end" for="permission-{{$permission->machine}}">
                        <div class="checkbox">
                            <input id="permission-{{$permission->machine}}" name="permissions[]" value="{{$permission->id}}" type="checkbox">
                            <span></span>
                        </div>
                    </label>
                </td>
                <td class="name">
                    <strong class="name">{{trans('permissions.' .$permission->machine)}}</strong>
                </td>
                <td class="machine">
                    <code>{{$permission->machine}}</code>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop

@section('footer')
    {!! Former::submit(trans('admin.create'))->class('large primary') !!}
    {!! Former::close() !!}
@stop