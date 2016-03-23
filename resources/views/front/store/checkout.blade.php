@extends('front.layout')

@section('title')
    Checkout - @parent
@stop

@section('breadcrumbs', Breadcrumbs::render('store.checkout'))

@section('article')
    <div class="row tight checkout-view">
        <div class="visible-l-up col-l-3 tight-left checkout-products">
            <?php
            $checkoutTag = Friluft\Tag::whereType('checkout')->first();
            ?>

            <div class="grid paper">
                @foreach($checkoutTag->products as $product)
                    @include('front.partial.products-block', ['product' => $product, 'withBuyButton' => true])
                @endforeach
            </div>
        </div>

        <div class="col-m-8 col-xl-7 col-l-6 cart">
            <div class="paper">
                @include('front.partial.cart-table', ['items' => $items, 'total' => $total, 'withShipping' => true, 'withTotal' => false])

				<hr>

				<div class="coupons-container">
					<h5>Rabattkoder</h5>

					<div class="errors alert warning" style="display: none">
						<h6>Oops!</h6>
						<p class="message"></p>
					</div>

					<div class="controls">
						<label for="code">Legg til Rabattkode
							<div class="input-append" style="width: 100%;">
								<input type="text" name="code">
								<div class="appended">
									<button class="add primary" style="flex: auto">Legg til</button>
								</div>
							</div>
						</label>
					</div>

					<div class="coupons">
						<ul class="flat">
							@foreach($coupons as $coupon)
							<li>
								<i class="fa fa-check color-success"></i> {{ $coupon->name }}
							</li>
							@endforeach
						</ul>
					</div>

					<?php $display = (count($coupons) > 0) ? 'block' : 'none'; ?>
					<p class="lead saved" style="display: {{$display}};">Du sparer <strong class="value">{{$saved}}</strong> kroner!</p>
				</div>

				<hr>

                <label class="checkbox-container end" style="float: left; width: 100%;" for="subscribe_to_newsletter">@lang('validation.attributes.subscribe_to_newsletter')
                    <div class="checkbox">
                        {!! Former::checkbox('subscribe_to_newsletter')->check()->label(null) !!}
                        <span></span>
                    </div>
                </label>

				<hr>

                {!! $snippet !!}
            </div>
        </div>

        <div class="visible-m-up col-m-4 col-l-3 col-xl-2 tight-right aside">
            <div class="block">
                <div class="module">
                    <div class="module-header">
                        <h5><i class="fa fa-fighter-jet"></i> &nbsp;&nbsp; Rask Levering</h5>
                    </div>
                    <div class="module-content">
                        {!! Friluft\Setting::whereMachine('checkout_1_content')->first()->value !!}
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="module">
                    <div class="module-header">
                        <h5><i class="fa fa-check-square-o"></i> &nbsp;&nbsp; Trygg Betaling</h5>
                    </div>
                    <div class="module-content">
                        {!! Friluft\Setting::whereMachine('checkout_2_content')->first()->value !!}
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="module">
                    <div class="module-header">
                        <h5><i class="fa fa-calendar"></i> &nbsp;&nbsp; 30 Dagers Åpent Kjøp</h5>
                    </div>
                    <div class="module-content">
                        {!! Friluft\Setting::whereMachine('checkout_3_content')->first()->value !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('scripts')
    @parent

    <script type="text/javascript">
        (function() {

            //hide the buy modal
            $("#buy-modal").remove();

            $(document).ready(function() {
                $("#klarna-checkout-container").css('overflow-x', 'visible');
            });

            //set subscribe to newsletter
            $("#subscribe_to_newsletter").change(function() {
                console.log('Subscribe changed!');
                var payload = {
                    _token: Friluft.token,
                };
                var val = 0;
                if ($(this).is(':checked')) val = 1;
                $.post(Friluft.URL + '/cart/setsubscribe/' + val, payload, function(response) {
					console.log('Set newsletter, response: ');
                    console.log(response);
                });
            });

			//set subscribe to newsletter default
			var val = 0;
			if ($('#subscribe_to_newsletter').is(':checked')) {
				val = 1;
			}
			$.post(Friluft.URL + '/cart/setsubscribe/' + val, {_token: Friluft.token}, function(response) {
				console.log('Set newsletter, response: ');
				console.log(response);
			});

			// coupon code handling
			var coupons = $('.coupons-container');
			coupons.find('.controls button.add').click(function() {
				var code = coupons.find('.controls input[name="code"]').val();
				if (code.length <= 0) return false;

				console.log('applying coupon code ' + code);

				//hide errors
				coupons.find('.errors').hide();

				klarnaSuspend();

				$.post(Friluft.URL + '/api/cart/coupons/' + code, {_method: 'PUT', _token: Friluft.token}, function(response) {
					console.log('response: ');
					console.log(response);

					if (response == 'invalid code') {
						//show error
						coupons.find('.errors .message').html("Beklager, koden er ikke riktig!").parent().show();
					}else if (response == 'unauthorized') {
						//show error
						coupons.find('.errors .message').html("Du må være <a href='" + Friluft.URL + "/user/login'>logget in</a> for å kunne bruke rabattkoder.").parent().show();
					}else if (response == 'already added') {
						coupons.find('.errors .message').html("Den koden er allerede lagt til.").parent().show();
					} else {
						//hide errors
						coupons.find('.errors .message').html('').parent().hide();

						//add coupon list item
						coupons.find('.coupons ul').append('<li><i class="fa fa-check color-success"></i> ' + response.name + '</li>');

						//update "saved"
						$.post(Friluft.URL + '/api/cart/saved', function(response) {
							coupons.find('.saved').show().find('.value').text(response);
						});
					}

					klarnaResume();
				});
			});

            // initialize isotope
			if ($('.products-grid').length > 0) {
				imagesLoaded(document.querySelector('.products-grid'), function() {
					$(".products-grid").isotope({
						itemSelector: '.product',
						layoutMode: 'fitRows',
					});
				});
			}
        })();
    </script>
@stop