{!! Former::open()
    ->method('POST')
    ->action(route('partners.post'))
    ->class('vertical')
    ->rules([
		'name' => 'required|max:255',
		'who' => 'required|max:255',
		'phone' => 'required',
		'email' => 'required|email',
		'about' => 'required',
		'website' => 'max:255',
	 	'instagram' => 'max:255',
    ])
 !!}

{!! Former::text('name') !!}

{!! Former::text('who')->label('Hvem representerer du?')->help('Lag, forening, bedrift eller deg selv?') !!}

{!! Former::text('phone') !!}

{!! Former::text('email') !!}

{!! Former::textarea('about')->label('Om deg/dere') !!}

{!! Former::text('website') !!}

{!! Former::text('instagram') !!}

{!! Former::submit('send')->class('large success') !!}

{!! Former::close() !!}