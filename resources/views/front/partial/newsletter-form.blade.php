<div class="newsletter-form">
	<div class="content">
		<h1 class="title">VINN PREMIER FOR 5000,-</h1>
		<h4 title="subtitle">Meld deg på vårt nyhetsbrev!</h4>

		<form action="{{route('nyhetsbrev.post')}}" method="POST" class="form inline">
			<input type="email" name="email" required placeholder="E-Mail Adresse" class="large">
			<input type="submit" value="Meld meg på!" class="large primary">
			<input type="hidden" name="_token" value="{!! csrf_token() !!}">
		</form>

		<p class="message end color-error"></p>
	</div>

	<div class="footer">
		<p>Vi trekker 3 heldige vinnere som får gavekort på henholdsvis 2500kr, 1500kr og 1000kr. Vinner trekkes 01.Mai 2016 og offentligjøres på
			<a href="http://www.123friluft.no/konkurranser">www.123friluft.no/konkurranser</a>.<br><small>Er du allerede nyhetsbrevmottaker er du med i trekningen.</small></p>
	</div>

	<div class="image"></div>
</div>