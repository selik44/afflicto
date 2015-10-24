<?php

namespace Friluft\Utils;

use Carbon\Carbon;

class LocalizedCarbon {

	private static $words = [
		'seconds',
		'second',
		'minutes',
		'minute',
		'days',
		'day',
		'weeks',
		'week',
		'months',
		'month',
		'years',
		'year',

		'from now',
		'in',
		'after',
		'ago',
	];

	public static function diffForHumans(Carbon $carbon, $a = null, $b = false) {
		$str = $carbon->diffForHumans($a, $b);

		foreach(static::$words as $word) {

			$str = str_replace($word, trans('carbon.' .$word), $str);
		}

		return $str;
	}

}