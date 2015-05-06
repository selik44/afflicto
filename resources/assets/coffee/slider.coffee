(($, window) ->
	class FriluftSlider

		defaults:
			pingPong: no
			delay: 2000
			transitionSpeed: 400

			slideLinks: yes
			nextLink: no
			prevLink: no
			touchControls: yes

			autoHeight: yes
			heightRatio: 0.7
			stopOnMouseEnter: no
			startOnMouseLeave: no

		constructor: (el, options) ->
			@options = $.extend({}, @defaults, options)
			@$el = $(el)

			@$container = @$el.find('> .container')
			@$slides = @$el.find('.slide')

			@currentIndex = 1
			@numSlides = @$slides.length

			if @options.slideLinks
				@createSlideLinks()
			
			# reLayout on resize
			$(window).resize _.debounce((() => @reLayout()), 30)

			# stop on mouse enter?
			if @options.stopOnMouseEnter
				@$el.mouseenter () =>
					@stop()

			# stop on mouse leave?
			if @options.startOnMouseLeave
				@$el.mouseleave () =>
					@start()

			# initialize touch control?
			if @options.touchControls is on
				@$el.swipe
					swipeStatus: @swipeStatus
					swipeLeft: @swipeLeft
					swipeRight: @swipeRight
					threshold: 200


			# relayout
			@reLayout()

			# start
			@start()

			return @

		swipeStatus: (event, phase, direction, distance, duration, fingers) =>
			if phase is 'start'
				# stop the interval
				@stop()

				# stop animating
				@$container.stop true, false

				# set the initial swipe position
				@initialSwipePosition = parseInt(@$container.css('left').replace(/[^-\d\.]/g, ''))
				return
			else if phase is 'cancel'
				# slide back
				@slide()
				@start()

				return
			else if phase is 'end'
				# slide back
				@slide()
				@start()
				return

			left = @initialSwipePosition

			if direction is 'left' then dist = -distance else dist = distance

			# follow fingers!
			@$container.css 'left': @initialSwipePosition + dist


		swipeRight: (event, direction, distance, fingerCount, fingerData) =>
			@goTo @currentIndex - 1

		swipeLeft: (event, direction, distance, fingerCount, fingerData) =>
			@goTo @currentIndex + 1

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

		slide: (speed = @options.transitionSpeed) ->
			left = -@$el.width() + (@$el.width() * @currentIndex)

			@$container.stop(true, false).animate {
					left: '-' + left
				}, speed
			
		reLayout: ->
			# set the width of each slide to the slider width
			@$slides.css 'width', @$el.width()


			# autoHeight?
			if @options.autoHeight
				width = @$el.width()
				height = width * @options.heightRatio
				@$el.css 'height', height

			@slide(0)


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