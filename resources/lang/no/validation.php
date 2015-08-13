<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => ":attribute må aksepteres.",
	"active_url"           => ":attribute er ikke en gyldig URL.",
	"after"                => ":attribute må være en dato etter :date.",
	"alpha"                => ":attribute kan bare bestå av bokstaver.",
	"alpha_dash"           => ":attribute kan bare bestå av bokstaver, tall and bindestreker.",
	"alpha_num"            => ":attribute may only contain bokstaver og tall.",
	"array"                => ":attribute må være en array/liste.",
	"before"               => ":attribute må væer en date etter :date.",
	"between"              => [
		"numeric" => ":attribute må være mellom :min and :max.",
		"file"    => ":attribute må være mellom :min and :max kilobyte.",
		"string"  => ":attribute må være mellom :min and :max tegn.",
		"array"   => ":attribute må ha mellom :min and :max elementer.",
	],
	"boolean"              => ":attribute må være sant eller usant.",
	"confirmed"            => ":attribute bekreftelsen stemmer ikke.",
	"date"                 => ":attribute er ikke en gyldig dato.",
	"date_format"          => ":attribute stemmer ikke med :format formatet.",
	"different"            => ":attribute og :other må være annerledes.",
	"digits"               => ":attribute må ha :digits siffer.",
	"digits_between"       => ":attribute må ha mellom :min og :max siffer.",
	"email"                => ":attribute må være en gyldig email addresse.",
	"filled"               => ":attribute er obligatorisk.",
	"exists"               => "valgt :attribute er ugyldig.",
	"image"                => ":attribute må være et bilde.",
	"in"                   => "valgt :attribute er ugyldig.",
	"integer"              => ":attribute må være et heltall.",
	"ip"                   => ":attribute må være en gyldig IP addresse.",
	"max"                  => [
		"numeric" => ":attribute kan ikke være mer enn :max.",
		"file"    => ":attribute kan ikke være mer enn :max kilobyte.",
		"string"  => ":attribute kan ikke lengre enn :max tegn.",
		"array"   => ":attribute kan ikke ha fler enn :max elementer.",
	],
	"mimes"                => ":attribute må være en fil av type: :values.",
	"min"                  => [
		"numeric" => ":attribute må være minst :min.",
		"file"    => ":attribute må være minst :min kilobyte.",
		"string"  => ":attribute må være minst :min tegn.",
		"array"   => ":attribute må ha minst :min elementer.",
	],
	"not_in"               => "valgt :attribute er ugyldig.",
	"numeric"              => ":attribute må være et nummer.",
	"regex"                => ":attribute formatet er ikke gyldig.",
	"required"             => ":attribute er obligatorisk.",
	"required_if"          => ":attribute er obligatorisk når :other er :value",
	"required_with"        => ":attribute er obligatorisk når :values er tilstede.",
	"required_with_all"    => ":attribute er obligatorisk når :values er tilstede.",
	"required_without"     => ":attribute er obligatorisk når :values ikke er tilstede.",
	"required_without_all" => ":attribute er ogligatorisk når ingen av :values er tilstede.",
	"same"                 => ":attribute og :other må være like.",
	"size"                 => [
		"numeric" => ":attribute må være :size.",
		"file"    => ":attribute må være :size kilobyte.",
		"string"  => ":attribute must være :size tegn.",
		"array"   => ":attribute må inneholde :size elementer.",
	],
	"unique"               => "Den :attributen er allerede tatt.",
	"url"                  => ":attribute formatet er ugyldig.",
	"timezone"             => ":attribute må være en gyldig tidssone.",
	"slug"                 => ":attribute må være en gyldig slug: bare små bokstaver og bindestrek.",

	'custom' => [
		'attribute-name' => [
			'rule-name' => 'custom-message',
		],
	],

	'disallowed_role_grant' => "Bare Administratorer kan gi en rolle annet en 'Regular'.",
	'disallow_default_role_delete' => ":role rollen kan ikke endres.",
	'disallow_admin_role_edit' => "Tillatelser for :role rollen kan ikke endres.",

	'attributes' => [
		'firstname' => 'Fornavn',
		'lastname' => 'Etternavn',
		'email' => 'E-Mail Addresse',
		'name' => 'Navn',
		'role' => 'Rolle',
		'machine' => 'Maskin-navn',
		'slug' => 'Slug (URL-navn)',
		'manufacturer' => 'Produsent',
		'category' => 'Kategori',
		'categories' => 'Kategorier',
		'weight' => 'Vekt (gram)',
		'price' => 'Pris',
		'In price' => 'Innkjøpspris',
		'vatgroup' => 'MVA Gruppe',
		'summary' => 'Kort beskrivelse',
		'description' => 'Full beskrivelse',
		'enabled' => 'Synlig',
		'articlenumber' => 'Artikkelnummer',
		'stock' => 'Lager',
		'password' => 'Passord',
		'password_confirmation' => 'Bekreft Passord',
		'old_password' => 'Nåværende Passord',
		'new_password' => 'Nytt Passord',
		'remember' => 'Husk Meg',
		'login' => 'Logg inn',
		'create account' => 'Lag Konto',
		'create' => 'Lag',
		'save' => 'Lagre',
		'user_id' => 'Kundenummer',
		'order_id' => 'Ordrenummer',
		'phone' => 'Telefon',
		'over_2_kg' => 'Pakken veier over 2kg',

		'billing_name' => 'Navn',
		'billing_postal_code' => 'Postkode',
		'billing_city' => 'By',
		'billing_country' => 'Land',
		'billing_email' => 'E-Mail Addresse',

		'shipping_name' => 'Navn',
		'shipping_postal_code' => 'Postkode',
		'shipping_city' => 'By',
		'shipping_country' => 'Land',
		'shipping_email' => 'E-Mail Addresse',

		'subscribe_to_newsletter' => 'Jeg ønsker å motta nyhetsbrev',
		'notify_user' => 'Varsle kunden',
		'message' => 'Melding',
	],

];
