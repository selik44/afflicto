@extends('admin.layout')

@section('title')
	@lang('admin.new') @lang('admin.category') - @parent
@stop

@section('header')
    <h2 class="title">@lang('admin.categories') - @lang('admin.new')</h2>
@stop

@section('content')
	<form class="vertical" action="{{url('admin/categories')}}" method="POST">
		<input type="hidden" name="_token" value="{{csrf_token()}}">
		<label for="name">Name <span class="color-error">*</span>
			<input type="text" name="name" maxlength="255" required>
		</label>

		<label for="slug">Slug <span class="color-error">*</span>
			<input type="text" name="slug" maxlength="255" required>
		</label>

        <label for="discount">@lang('admin.discount')
            <div class="input-append">
                <input type="text" name="discount" value="0">
                <span class="appended">%</span>
            </div>
        </label>
        <br>

		<label for="parent">Parent
			<select id="categories-select" name="parent_id">
				<option value="null">None</option>
				@foreach($categories as $cat)
					<option value="{{$cat->id}}">{{$cat->name}}</option>
				@endforeach
			</select>
		</label>

        {!! Former::textarea('meta_description')->rows(4); !!}

        {!! Former::textarea('meta_keywords')->rows(4); !!}

        <div class="button-group">
            <input type="submit" class="primary" name="create" value="Create">
            <input type="submit" class="secondary" name="continue" value="Create & Continue">
        </div>

    </form>
@stop

@section('footer')
@stop

@section('scripts')
	@parent
	<script type="text/javascript">
		var form = $("form");
		
		form.find('#categories-select').chosen();

		form.find('[name="slug"]').autoSlug({other: '[name="name"]'});
	</script>
@stop