<?php namespace Friluft\Utils;

use Curl\Curl;

class Mystore {
	
	const URL = "https://mystore-api.no";
	
	protected static function get($resource, $params = []) {
		$params['api_key'] = getenv('MYSTORE_API_KEY');

		$curl = new Curl();
		$curl->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$curl->get(self::URL .'/' .$resource, $params);
		return json_decode($curl->response, true);
	}

	public static function products($page = 1) {
		$data = self::get('products.json', ['page' => $page]);
		if (isset($data['data'])) return $data['data']['product_data'];
		return [];
	}

	public static function categories() {
		$data = self::get('categories.json');
		if (isset($data['data'])) return $data['data']['category_data'];
		return [];
	}

}