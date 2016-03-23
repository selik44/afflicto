<?php

namespace Friluft\Page;

use Illuminate\Support\Facades\Facade;

class Pages extends Facade
{

	protected static function getFacadeAccessor()
	{
		return 'pages';
	}

}