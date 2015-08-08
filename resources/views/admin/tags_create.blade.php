@extends('admin.layout')

@section('title')
	@lang('admin.new') @lang('admin.tag') - @parent
@stop

@section('page')
	<h2>New Tag</h2>
	<form class="vertical" action="{{route('admin.tags.store')}}" method="POST">
		<input type="hidden" name="_token" value="{{csrf_token()}}">
		<label for="label">Label <span class="color-error">*</span>
			<input type="text" name="label" maxlength="255" required>
		</label>

		<label for="icon">Icon
			<select id="icon-selector" name="icon">
				<option value="null">None</option>
				@foreach($icons as $icon)
					<option value="fa fa-{{$icon}}">fa fa-{{$icon}}</option>
				@endforeach
			</select>
		</label>

        <label for="color">Color
            <input type="color" name="color">
        </label>

        {!! Former::checkbox('visible') !!}

		<div class="footer-height-fix"></div>

		<footer id="footer">
			<div class="inner">
				<div class="button-group">
					<input type="submit" class="primary" name="create" value="Create">
					<input type="submit" class="secondary" name="continue" value="Create & Continue">
				</div>
			</div>
		</footer>

	</form>
@stop

@section('scripts')
	@parent
	<script type="text/javascript">
		$('#icon-selector').fontIconPicker();
	</script>
@stop