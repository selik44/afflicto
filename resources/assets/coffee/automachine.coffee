(($, window) ->
  class AutoMachine

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

        #turn spaces and dashes into underscores
        value = value.replace(/[ -]/g, '_')

        #singularize dashes -- becomes -
        value = value.replace(/[_]{1,}/g, '_')

        #trim
        value = value.replace(/(^_)|(_$)/, '')

        @$el.val(value)


      #set slug custom
      @$el.bind 'keyup', ->
        $(this).addClass('custom')
        if $(this).val().length == 0
          $(this).removeClass('custom')






  # Define the plugin
  $.fn.extend autoMachine: (option, args...) ->
    @each ->
      $this = $(this)
      data = $this.data('autoMachine')

      if !data
        $this.data 'autoMachine', (data = new AutoMachine(this, option))
      if typeof option == 'string'
        data[option].apply(data, args)

) window.jQuery, window