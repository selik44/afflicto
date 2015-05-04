(($, window) ->
	class AutoSlug

		defaults:
			other: null

		constructor: (el, options) ->
			@options = $.extend({}, @defaults, options)
			@$el = $(el)
			@$other = $(@options.other);

			console.log @options

			# listen for other change, then slugify @$el value via .val()
			@$other.bind 'keyup change', =>
				value = @$other.val()

				if @$el.hasClass('custom') then return
					
				#to lower case
				value = value.toLowerCase()

				#remove unwanted characters
				value = value.replace(/[^a-z0-9-_ ]/g, '')

				#turn spaces and underscores into dashes
				value = value.replace(/[ _]/g, '-')

				#singularize dashes -- becomes -
				value = value.replace(/[-]{1,}/g, '-')

				#trim
				value = value.replace(/(^-)|(-$)/, '')

				@$el.val(value)


			#set slug custom
			@$el.bind 'keyup', ->
				$(this).addClass('custom')
				if $(this).val().length == 0
					$(this).removeClass('custom')
				
		




	# Define the plugin
	$.fn.extend autoSlug: (option, args...) ->
		@each ->
			$this = $(this)
			data = $this.data('autoSlug')

			if !data
				$this.data 'autoSlug', (data = new AutoSlug(this, option))
			if typeof option == 'string'
				data[option].apply(data, args)

) window.jQuery, window