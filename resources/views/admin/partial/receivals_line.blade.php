<tr class="product product-{{$product->id}} {{($product->hasVariants()) ? 'variants' : ''}}" data-id="{{$product->id}}">
	<td class="name">
		{{$product->name}}
	</td>

	<td class="options">
		@if($product->hasVariants())
			<table>
				<?php

				$stock = ($product->variants_stock) ? $product->variants_stock : [];

				$rootVariant = $product->variants[0];
				if (count($product->variants) > 1) {
					foreach($rootVariant->data['values'] as $rootValue) {
						foreach($product->variants as $variant) {
							if ($rootVariant == $variant) continue;

							foreach($variant['data']['values'] as $value) {
								$stockID = $rootValue['id'] .'_' .$value['id'];
								$s = 0;
								if (isset($stock[$stockID])) {
									$s = $stock[$stockID];
								}

								echo '<tr class="variant-' .$stockID .'" data-stock-id="' .$stockID .'">';
									echo '<td class="name">' .$rootValue['name'] .' ' .$value['name'] .'</td>';
									echo '<td class="stock"><input type="text" value="' .$s .'"></td>';
									echo '<td class="order"><input type="text" value="0"></td>';
								echo '<tr>';
							}
						}
					}
				}else {
					echo '<tr>
							<th>Navn</th>
							<th>Lager</th>
							<th>Antall</th>
						</tr>';

					foreach($rootVariant->data['values'] as $value) {
						$stockID = $value['id'];

						$s = 0;
						if (isset($stock[$stockID])) {
							$s = $stock[$stockID];
						}
						echo '<tr class="variant variant-' .$value['id'] .'" data-stock-id="' .$stockID .'">';
							echo '<td class="name">' .$value['name'] .'</td>';
							echo '<td class="stock">' .$s .'</td>';
							echo '<td class="quantity"><input type="text" value="0"></td>';
						echo '</tr>';
					}
				}
				?>
			</table>
		@else
			<table>
				<tr>
					<th>Lager</th>
					<th>Antall</th>
				</tr>

				<tr>
					<td>{{$product->stock}}</td>
					<td class="quantity">
						<input type="text" value="0">
					</td>
				</tr>
			</table>
		@endif
	</td>

	<td class="controls">
		<button class="error remove"><i class="fa fa-close"></i></button>
	</td>
</tr>