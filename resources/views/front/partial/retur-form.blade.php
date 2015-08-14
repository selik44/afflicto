{!! Former::open()
    ->method('POST')
    ->action(route('retur.post'))
    ->class('vertical')
    ->rules([
        'name' => 'required',
        //'order_id' => 'required|exists:orders,id',
        'order_id' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'varer' => 'required',
        'cause' => 'required',
    ])
 !!}

{!! Former::text('name') !!}

{!! Former::text('phone') !!}

{!! Former::email('email') !!}

{!! Former::textarea('varer') !!}

<hr>

{!! Former::text('user_id') !!}

{!! Former::text('order_id') !!}

{!! Former::radios('cause')->radios(['retur', 'bytte', 'reklamasjon']) !!}

{!! Former::checkbox('over_2_kg')->value('1') !!}

{!! Former::submit('send')->class('large success') !!}

{!! Former::close() !!}