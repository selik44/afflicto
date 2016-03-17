<div id="newsletter-popup" class="modal newsletter-popup">
	<a href="#" data-toggle-modal="#newsletter-popup" class="modal-dismiss"><i class="fa fa-close"></i></a>

	<div class="modal-content">
		@include('front.partial.newsletter-form')

		<div class="alert end" style="display: none; margin-top: 1rem;">
			<p class="message"></p>
		</div>
	</div>
</div>

@section('scripts')
	@parent
	<script type="text/javascript">
		(function(window, document, $, undefined) {
			var modal = $("#newsletter-popup");
			modal.gsModal("show");

			var form = modal.find('form');

			var alert = modal.find('.alert').detach();
			alert.hide();
			form.append(alert);

			form.bind('submit', function(e) {
				e.preventDefault();
				alert.slideUp();

				var payload = {
					'email': form.find('[name="email"]').val(),
					'_token': Friluft.token,
				};

				$.post(Friluft.URL + "/api/newsletter", payload, function(response) {


					if (response.status == 'success') {
						console.log('success!');

						alert.addClass('success').removeClass('error');
						alert.find('.message').html('Du er nå registrert!');
						alert.slideDown(300);

						form.replaceWith(alert.detach());

						setTimeout(function() {
							modal.gsModal('hide');
						}, 2000);
					}else {
						console.log('error!');
						alert.removeClass('success').addClass('error');
						alert.find('.message').html(response.error);
						alert.slideDown();
					}
				});
			});
		})(window, document, $);
	</script>
@stop