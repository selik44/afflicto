@extends('admin.layout')

@section('title')
	@lang('admin.dashboard') - @parent
@stop

@section('header')
	<h2 class="title">@lang('admin.dashboard')</h2>
@stop

@section('scripts')
	@parent

	<script src="http://cdn.zingchart.com/zingchart.min.js"></script>

	<script>

		var data = {
			type: "line",
			'scale-x': {
				'label': {
					'text': 'Date',
					'values': JSON.parse("{!! addcslashes($labels, '"') !!}"),
				},
			},
			'scale-y': {
				'label': {
					'text': 'Profit',
				},
				"format": "%v",
				"negation": "currency",
				"thousands-separator":",",
			},
			'plot': {
				"format": "%v,-",
				"negation": "currency",
				"thousands-separator":",",
			},
			series: [
				{
					//values: [249, 2601, 635, 0, 15234/*, 520, 724, 6346, 9592, 246, 592, 4343, 49, 249, 2601, 635, 0, 15234, 520, 724, 6346, 9592, 246, 592, 4343, 49, 531, 0, 0, 0, 631*/]
					values: JSON.parse("{!! addcslashes($values, '"') !!}"),
				}
			]
		};

		zingchart.render({
			id: 'profit-chart',
			data: data,
		});
	</script>
@stop

@section('content')
	<div class="col-xs-6">
		<div class="module" id="profit">
			<div class="module-header clearfix">
				<h6 class="pull-left">@lang('admin.profit')</h6>
				<form action="{{route('admin.dashboard')}}" method="GET" class="inline pull-right">
					<label for="from">Fra
						<input type="date" name="from" value="{{$from}}">
					</label>

					<label for="to">Til
						<input type="date" name="to" value="{{$to}}">
					</label>

					<input type="submit" class="success" value="Oppdater">
				</form>
			</div>
			<div class="module-content">
				<div id="profit-chart"></div>
			</div>
		</div>
	</div>

	<div class="col-xs-6">
		<div class="module" id="stock">
			<div class="module-header clearfix">
				<h6 class="pull-left">Lagerstatus</h6>
			</div>

			<div class="module-content">
				<table>
					<tbody>
					@foreach($stock as $item)
						<tr
								@if($item['stock'] <= 0)
									class="color-error"
								@elseif ($item['stock'] == 1)
									class="color-warning"
								@endif
							>
							<td class="name">{{$item['product']->name}}</td>
							<td class="stock">{{$item['stock']}}</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@stop