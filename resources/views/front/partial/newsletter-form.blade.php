<div class="newsletter-form">
	<div class="content">
		<h1 class="title">VINN PREMIER FOR 5000,-</h1>
		<h4 title="subtitle">Meld deg på vårt nyhetsbrev!</h4>

		<form action="{{route('nyhetsbrev.post')}}" method="POST" class="form inline">
			<input type="email" name="email" required placeholder="E-Mail Adresse" class="large">
			<input type="submit" value="Meld meg på!" class="large primary">
			<input type="hidden" name="_token" value="{!! csrf_token() !!}">
		</form>
	</div>

	<div class="image"></div>
</div>