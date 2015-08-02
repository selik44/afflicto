<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Order;
use Friluft\Utils\XML;
use Illuminate\Http\Request;

class ProteriaController extends Controller {

	public function getExport() {
		$orders = Order::whereStatus('ready_for_sending')->get();

		$xml = new XML('FraktXml');

		foreach($orders as $order) {
			$shipping = $order->getShipping();

			$entry = [
				'OrdreNr' => $order->id,
				'AntKolli' => 1,
				'HvemBetaler' => 1,	//mottaker
				'Kolli' => [
					'Vekt' => $order->getWeight(),
				],
				'Mottaker' => [
					'KundeNr' => $order->user->id,
					'Navn' => $order->billing_address['given_name'] .' ' .$order->billing_address['family_name'],
					'PostAdr1' => $order->billing_address['street_address'],
					'PostNr' => $order->billing_address['postal_code'],
					'PostSted' => $order->billing_address['city'],

					'LevAdr1' => $order->shipping_address['street_address'],
					'LevPostNr' => $order->shipping_address['postal_code'],
					'LevPostSted' => $order->shipping_address['city'],
					'KontaktPerson' => $order->user->name,
					'Email' => $order->shipping_address['email'],
					'Mobil' => str_replace(' ', '', $order->shipping_address['phone']),
				],
			];

			if ($shipping['name'] == 'mail') {
				//brevpost
				$entry['SendingsType'] = 7; //brevetikett
				$entry['PakkeType'] = 120; //brevpost innland
				$entry['Franko'] = 1; //Frankopåtrykk, A-post
			}else {
				//service pakke
				$entry['SendingsType'] = 101; //bring parcels
				$entry['PakkeType'] = 1100; //klimanøytral service pakke
			}

			# add the entry
			$xml->add('Sending', $entry);
		}

		# return as plain text for now
		return \Response::make($xml->render(true), 200, ['Content-Type' => 'text/xml']);
	}

}
