@extends('admin.layout')

@section('title')
    @lang('admin.edit') - @lang('admin.roles') - @parent
@stop

@section('page')
    <h2 class="end">@lang('admin.roles') <span class="muted">{{$role->name}}</span></h2>
    <hr/>

    {!! $form->open
    ->action(route('admin.roles.update', ['role' => $role->id]))
    ->method('PUT')
    !!}

    {!! $form->name !!}

    <hr/>

    <h4>Permissions</h4>
    <table class="permissions-table bordered striped boxed">
        <tbody>
        @foreach($permissions as $permission)
            <tr>
                <td class="input" style="width: 40px">
                    <label class="checkbox-container end" for="permission-{{$permission->machine}}">
                        <div class="checkbox">
                            @if($role->permissions->contains($permission))
                                <input checked id="permission-{{$permission->machine}}" name="permissions[]" value="{{$permission->id}}" type="checkbox">
                                <span></span>
                            @else
                                <input id="permission-{{$permission->machine}}" name="permissions[]" value="{{$permission->id}}" type="checkbox">
                                <span></span>
                            @endif
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

    <div class="footer-height-fix" style="height: 120px">
    </div>

    <div id="footer">
        <div class="inner">
            {!! Former::submit(trans('admin.save'))->class('large success') !!}
            {!! Former::close() !!}
        </div>
    </div>
@stop