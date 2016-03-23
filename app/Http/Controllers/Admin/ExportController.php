<?php

namespace Friluft\Http\Controllers\Admin;

use Friluft\Product;
use Illuminate\Http\Request;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ExportController extends Controller
{
    /**
     * Export products to Excel
     *
     * @return \Illuminate\Http\Response
     */
    public function products()
    {
		return Excel::create('products', function(LaravelExcelWriter $excel) {
			$excel->setTitle('123Friluft Export - Produkter');
			$excel->sheet('Produkter', function(\PHPExcel_Worksheet $sheet) {
				$products = Product::query()->get();

				$sheet->row(1, ['Produsent', 'Navn', 'Art. Nummer', 'Lagerplass', 'Innkjøpspris', 'Stock']);

				$i = 2;
				foreach($products as $product) {
					/**
					 * @var Product $product
					 */

					# skip products
					#   - that aren't in stock
					#   - kombo's
					if ($product->getTotalStock() <= 0 || $product->isCompound()) continue;

					$manufacturer = $product->manufacturer ? $product->manufacturer->name : '';
					$sheet->row($i, [$manufacturer, $product->name, $product->articlenumber, $product->barcode, $product->inprice, $product->getTotalStock()]);
					$i++;
				}
			});
		})->download();
    }

}
