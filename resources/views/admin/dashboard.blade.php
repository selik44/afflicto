@extends('admin.layout')

@section('title')
	Dashboard - @parent
@stop

@section('page')
	<div class="col-m-6 col-l-4">
		<div class="module">
			<div class="module-header"><h6>New Orders</h6></div>
			<div class="module-content">
				<ul class="flat">
					<li><a href="#">Someone bought something (#23)</span></a></li>
					<li><a href="#">Some order (#24)</span></a></li>
					<li><a href="#">Another one (#25)</span></a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="col-m-6 col-l-4">
		<div class="module">
			<div class="module-header"><h6>Something Else</h6></div>
			<div class="module-content">
				<ul class="flat">
					<li><a href="#">Some order</a></li>
				</ul>
			</div>
		</div>
	</div>

	<hr class="visible-m">

	<div class="col-m-6 col-l-4">
		<div class="module">
			<div class="module-header"><h6>New Orders</h6></div>
			<div class="module-content">
				<ul class="flat">
					<li><a href="#">Someone bought something (#23)</span></a></li>
					<li><a href="#">Some order (#24)</span></a></li>
					<li><a href="#">Another one (#25)</span></a></li>
				</ul>
			</div>
		</div>
	</div>
	
	<hr class="visible-l-up">

	<div class="col-m-6 col-l-4">
		<div class="module">
			<div class="module-header"><h6>Something Else</h6></div>
			<div class="module-content">
				<ul class="flat">
					<li><a href="#">Some order</a></li>
				</ul>
			</div>
		</div>
	</div>
@stop