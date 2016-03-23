{!! Former::open()
    ->method('POST')
    ->action(route('contact.post'))
    ->class('vertical')
    ->rules([
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'message' => 'required',
    ])
 !!}

{!! Former::text('name') !!}

{!! Former::text('phone') !!}

{!! Former::email('email') !!}

{!! Former::textarea('message') !!}

<hr>

{!! Former::text('user_id') !!}

{!! Former::text('order_id') !!}

{!! Former::submit('send')->class('large success') !!}

{!! Former::close() !!}