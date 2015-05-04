(($, window) ->
	class FriluftSlider

		defaults:
			pingPong: off
			delay: 2000
			transitionSpeed: 400

			slideLinks: on
			nextLink: off
			prevLink: off
			touchControls: off

		constructor: (el, options) ->
			@options = $.extend({}, @defaults, options)
			@$el = $(el)

			@$container = @$el.find('> .container')
			@$slides = @$el.find('.slide')

			@currentIndex = 1
			@numSlides = @$slides.length

			if @options.slideLinks
				@createSlideLinks()

			# relayout
			@reLayout()

			# start
			@start()

			# setup some event listeners
			$(window).resize () =>
				@reLayout()

			@$el.mouseenter () =>
				@stop()

			@$el.mouseleave () =>
				@start()

			return @

		createSlideLinks: ->
			self = @

			# create slide-link element
			@$el.append('<ul class="inline slide-links"/>');
			@$slideLinks = @$el.find('.slide-links')

			# create links
			i = 1
			for slide in @$slides
				@$slideLinks.append '<li><a href="#" data-id="' + i + '"><i class="fa fa-circle"></a></li>'
				i++

			@setCurrentSlideLink()

			@$slideLinks.find('li a').click () ->
				self.stop()
				self.goTo($(this).attr('data-id'))

		goTo: (index) ->
			console.log 'going to ' + index
			@currentIndex = index
			@updateIndex()
			@setCurrentSlideLink()
			@slide()

		setCurrentSlideLink: () ->
			if @options.slideLinks
				@$slideLinks.find('li a.current').removeClass 'current'
				@$slideLinks.find('li a[data-id="' + @currentIndex + '"]').addClass 'current'

		start: ->
			@interval = setInterval =>
				@next()
				@slide()
			, @options.delay

			return @

		stop: ->
			clearInterval @interval
			return @

		next: ->
			@currentIndex++
			@updateIndex()

			return @

		prev: ->
			@currentIndex--
			@updateIndex()
			return @

		updateIndex: ->
			if @currentIndex > @numSlides
				@currentIndex = 1
			else if @currentIndex < 1
				@currentIndex = 1

			@setCurrentSlideLink()

			return @

		slide: ->
			left = -@$el.width() + (@$el.width() * @currentIndex)

			@$container.animate {
					left: '-' + left
				}, @options.transitionSpeed
			
		reLayout: ->
			# set the width of each slide to the slider width
			@$slides.css 'width', @$el.width()


	# Define the plugin
	$.fn.extend friluftSlider: (option, args...) ->
		@each ->
			$this = $(this)
			data = $this.data('friluftSlider')

			if !data
				$this.data 'friluftSlider', (data = new FriluftSlider(this, option))
			if typeof option == 'string'
				data[option].apply(data, args)

) window.jQuery, window