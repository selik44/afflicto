@extends('master')

@section('scripts')
	<script>
		window.print();
	</script>
@stop

@section('body')
	<style>
		body {
			float: none;
			overflow: visible;
		}

		.container {
			float: none
		}

		.packlist {
			float: none
		}
		.packlist:not(:last-child) {
			page-break-after: always;
		}

		hr {
			background-color: #ddd
		}
	</style>

	<div class="container ninesixty" style="width: 780px;">
		@foreach($receivals as $key => $receival)
			<div class="row clearfix packlist" style="margin: 0; padding: 0;">
				<div class="row">
					<div class="col-xs-6 tight-left">
						<img id="logo" src="{{asset('images/friluft.png')}}" alt="logo"/>
					</div>

					<div class="col-xs-6 tight-right text-right">
						123Concept AS<br>
						Postboks 27<br>
						1751 Halden
					</div>
				</div>

				<hr class="small">

				<div class="col-xs-12 tight">
					<ul class="inline text-center end">
						<li>...</li>
					</ul>
				</div>

				<hr/>

				<div class="row tight">
					<div class="col-xs-12 tight">
						<table class="bordered">
							<thead>
								<tr>
									<th>Article number</th>
									<th>Name</th>
									<th>Purchase Price</th>
									<th>Amount</th>
								</tr>
							</thead>

							<tbody>
							@foreach($receival->getProductsWithModels() as $product)
								<?php
									$model = $product['model'];
								?>
								<tr>
									<td>{{$model->articlenumber}}</td>
									<td>{{$model->name}}</td>
									<td>{{$model->inprice}}</td>
									<td>
										@if($model->hasVariants())
											<table>
												@foreach($model->getVariantChoices() as $choice)
													<tr>
														<td><strong>{{$choice['name']}}</strong></td>
														<td>{{$product['order'][$choice['id']]}}</td>
													</tr>
												@endforeach
											</table>
										@else
											<td class="text-right">
												{{$product['order']}}
											</td>
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		@endforeach
	</div>
@stop