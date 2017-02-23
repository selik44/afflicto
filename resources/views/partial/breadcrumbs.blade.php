@if ($breadcrumbs)
	<ul class="breadcrumbs end">
		@foreach ($breadcrumbs as $breadcrumb)
			@if ($breadcrumb->url && !$breadcrumb->last)
				<li><a href="{{{ $breadcrumb->url }}}">{{{ $breadcrumb->title }}}</a></li>
			@else
				<li class="disabled">{{{ $breadcrumb->title }}}</li>
			@endif
		@endforeach
	</ul>
@endif