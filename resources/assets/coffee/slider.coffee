(($, window) ->

	isFunc = (func) ->
		getType = {}
		func and getType.toString.call func is '[object Function]'

	class FriluftSlider

		defaults:
			pingPong: no
			delay: 2000
			transitionSpeed: 300

			slideLinks: yes
			nextLink: no
			prevLink: no
			touchControls: yes

			autoHeight: yes
			heightRatio: 0.7
			stopOnMouseEnter: no
			startOnMouseLeave: no

			useElements: no

		constructor: (el, options) ->
			@options = $.extend({}, @defaults, options)
			@$el = $(el)

			@$container = @$el.find('> .container')
			@$slides = @$el.find('.slide')

			@currentIndex = 1
			@numSlides = @$slides.length

			if @options.slideLinks
				@createSlideLinks()

			@elementTimeouts = []

			if @options.useElements
				@initializeElements()

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
					threshold: 100
					allowPageScroll: "vertical"


			# relayout
			@reLayout()

			# start
			@start()

			return @

		elementPosition: (pos, x, y) ->
			x = parseInt x
			y = parseInt y

			w = @$el.width() / 2
			h = @$el.height() / 2

			console.log 'width: ' + w
			console.log 'height: ' + h

			console.log 'pos: ' + x + ', ' + y

			if pos is 'left'
				return left: x, top: h + y
			else if pos is 'right'
				return right: x, top: h + y
			else if pos is 'top'
				return left: w + x, top: y
			else if pos is 'bottom'
				return bottom: y, left: h + x
			else if pos is 'top_left'
				return left: x, top: y
			else if pos is 'top_right'
				return right: x, top: y
			else if pos is 'bottom_left'
				return left: x, bottom: x
			else if pos is 'bottom_right'
				return right: x, bottom: y
			else if pos is 'center'
				return left: w + x, top: h + x

		# initialize elements start position
		initializeElements: ->
			that = this
			@$container.find('.element').each () ->
				el = $(this)
				start = el.attr 'data-start'
				x = el.attr 'data-offset-x'
				y = el.attr 'data-offset-y'

				pos = that.elementPosition start, x, y

				el.css pos
				el.css 'opacity', 0

		showElement: (el) ->
			end = el.attr('data-end')
			pos = @elementPosition end, parseInt(el.attr('data-offset-x')), parseInt(el.attr('data-offset-y'))
			pos.opacity = 1
			el.animate(pos, el.attr('data-speed'))

		hideElement: (el) ->
			el.css 'opacity', 0

		swipeStatus: (event, phase, direction, distance, duration, fingers) =>
			console.log(event);

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

			# at end?
			if @currentIndex <= 1 and dist > 100 then dist = 100
			else if @currentIndex == @numSlides and dist < -100 then dist = -100

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
				self.start()

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
			@$el.trigger 'slider.next'
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
			that = this

			if @options.useElements is true
				for timeout in @elementTimeouts
					clearTimeout timeout
				@elementTimeouts = []

				for slide in @$slides
					$(slide).find('.element').each () ->
						pos = that.elementPosition($(this).attr('data-start'), $(this).attr('data-offset-x'), $(this).attr('data-offset-y'))
						$(this).animate pos
						$(this).animate {'opacity': 0}, that.options.transitionSpeed

				# current slide
				slide = that.$slides[that.currentIndex-1]
				$(slide).find('.element').each () ->
					delay = $(this).attr 'data-delay'
					$(this).css 'opacity', 0

			@$container.stop(true, false).animate {
				left: '-' + left
			}, speed, =>
				if @options.useElements is true
					slide = that.$slides[that.currentIndex-1]
					$(slide).find('.element').each () ->
						delay = $(this).attr 'data-delay'
						el = $(this)
						that.elementTimeouts.push setTimeout ->
								that.showElement el
							, delay


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
			slider = $this.data('friluftSlider')

			if !slider
				$this.data 'friluftSlider', (slider = new FriluftSlider(this, option))
			else if typeof option == 'string'
				if option == "option"
					console.log 'getting option'
					if args[1]?
						slider.options[args[0]] = args[1]
					else
						return slider.options[args[0]]
				else
					console.log 'getting property or calling method'
					if typeof slider[option] is 'function'
						console.log 'calling method'
						slider[option].apply slider, args
					else
						console.log 'getting prop'
						console.log 'slider.' + option + ' is ' + slider[option]
						return slider[option]

) window.jQuery, window