<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Order;
use Friluft\Utils\XML;
use Illuminate\Http\Request;

class ProteriaController extends Controller {

	public function getExport() {
		$orders = Order::whereStatus('ready_for_sending')->get();
		$xml = new XML('FraktXml', XML::ISO_8859_1);

		foreach($orders as $order) {
			$shipping = $order->getShipping();

			$entry['OrdreNr'] = $order->id;

			if ($shipping['name'] == 'mail') {
				//brevpost
				$sendingsType = 7; //brevetikett
				$pakkeType = 120; //brevpost innland
				$franko = 1; //Frankopåtrykk, A-post
			}else {
				//service pakke
				$sendingsType = 101; //bring parcels
				$pakkeType = 1100; //klimanøytral service pakke
				$franko = null;
			}

			$entry = [
				'SendingsType' => $sendingsType,
				'PakkeType' => $pakkeType,
				'AntKolli' => 1,
				'HvemBetaler' => 1,	//mottaker
				'Franko' => $franko,
				'Kolli' => [
					'Vekt' => $order->getWeight() / 1000,
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
				'Tjenester' => [
					'SendAutoMail' => 1,
					'T20' => 1,
				],
			];

			# add the entry
			$xml->add('Sending', $entry);
		}

		# return as plain text for now
		return \Response::make($xml->render(false), 200, ['Content-Type' => 'text/xml; charset=ISO-8859-1']);
	}

	/**
	 * URL-eksport
	-----------
	For å eksportere til en url (script), gjør følgende:
	Gå inn på Vedlikehold -> Innstillinger -> Standardverdier, velg "Eksporter til URL".
	Skriv inn en gyldig url til et script som kan ta imot og behandle de data som blir sendt (f.eks. en php- eller en asp-fil).

	Scriptet du angir der vil bli kalt hver gang du printer ut en etikett.
	Følgende parametre blir sendt med:
	- Sendingstype
	- Sendingsnr
	- Ordrenr
	- PortoUtenMva
	- PortoMedMva
	 */
	public function update() {
		if (!Input::has('Ordernr') || Input::has('Sendingsnr')) {
			return response('error', 400);
		}
		$order = Order::find(Input::get('Ordrenr'));

		# update order
		$order->save();
	}

}
