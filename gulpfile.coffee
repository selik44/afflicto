gulp = require 'gulp'
util = require 'gulp-util'
sass = require 'gulp-sass'
coffee = require 'gulp-coffee'
concat = require 'gulp-concat'
es = require 'event-stream'
order = require 'gulp-order'
prefixer = require 'gulp-autoprefixer'

# SASS
gulp.task 'sass', ->
	gulp.src ['resources/assets/sass/friluft/friluft.sass', 'resources/assets/sass/highpulse/highpulse.sass']
	.pipe sass
			indentedSyntax: true
			onError: util.log
	.pipe prefixer()
	.pipe gulp.dest 'public/css'


# SCRIPTS
gulp.task 'scripts', ->
	jsFiles = gulp
		.src [
			'resources/assets/vendor/jquery/dist/jquery.min.js'
			'resources/assets/vendor/underscore/underscore-min.js'
			'resources/assets/vendor/jquery-touchswipe/jquery.touchSwipe.min.js'
			'resources/assets/vendor/isotope/dist/isotope.pkgd.min.js'
			'resources/assets/vendor/isotope-packery/packery-mode.pkgd.min.js'
			'resources/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js'
			'resources/assets/vendor/nouislider/distribute/jquery.nouislider.all.min.js'
			'resources/assets/vendor/chosen/chosen.jquery.min.js'
			  'resources/assets/vendor/html.sortable/dist/html.sortable.min.js'
			  'resources/assets/vendor/dropzone/dist/min/dropzone.min.js'
			'node_modules/@afflicto/gentlestyle/dist/gentlestyle.js'
		]

	coffeeFiles = gulp
		.src [
				'resources/assets/coffee/**/*.coffee'
		]
		.pipe coffee().on 'error', util.log

	# merge them
	jsFiles = es.merge [jsFiles, coffeeFiles]

	jsFiles
		.pipe order [
				'jquery.min.js'
				'chosen.jquery.min.js'
				'gentlestyle.js'
				'autoslug.coffee'
				'slider.coffee'
			]
		.pipe concat 'all.js'
		.pipe gulp.dest 'public/js'


# WATCH
gulp.task 'watch', ->
	gulp.watch ['resources/assets/sass/**/*.sass', 'resources/assets/coffee/**/*.coffee'], ['sass', 'scripts']
	


# DEFAULT
gulp.task 'default', ->
	gulp.run 'sass'
	gulp.run 'scripts'
	gulp.run 'watch'