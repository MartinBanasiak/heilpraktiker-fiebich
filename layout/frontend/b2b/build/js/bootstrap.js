/* ========================================================================
 * Bootstrap: affix.js v3.3.7
 * http://getbootstrap.com/javascript/#affix
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // AFFIX CLASS DEFINITION
  // ======================

  var Affix = function (element, options) {
    this.options = $.extend({}, Affix.DEFAULTS, options)

    this.$target = $(this.options.target)
      .on('scroll.bs.affix.data-api', $.proxy(this.checkPosition, this))
      .on('click.bs.affix.data-api',  $.proxy(this.checkPositionWithEventLoop, this))

    this.$element     = $(element)
    this.affixed      = null
    this.unpin        = null
    this.pinnedOffset = null

    this.checkPosition()
  }

  Affix.VERSION  = '3.3.7'

  Affix.RESET    = 'affix affix-top affix-bottom'

  Affix.DEFAULTS = {
    offset: 0,
    target: window
  }

  Affix.prototype.getState = function (scrollHeight, height, offsetTop, offsetBottom) {
    var scrollTop    = this.$target.scrollTop()
    var position     = this.$element.offset()
    var targetHeight = this.$target.height()

    if (offsetTop != null && this.affixed == 'top') return scrollTop < offsetTop ? 'top' : false

    if (this.affixed == 'bottom') {
      if (offsetTop != null) return (scrollTop + this.unpin <= position.top) ? false : 'bottom'
      return (scrollTop + targetHeight <= scrollHeight - offsetBottom) ? false : 'bottom'
    }

    var initializing   = this.affixed == null
    var colliderTop    = initializing ? scrollTop : position.top
    var colliderHeight = initializing ? targetHeight : height

    if (offsetTop != null && scrollTop <= offsetTop) return 'top'
    if (offsetBottom != null && (colliderTop + colliderHeight >= scrollHeight - offsetBottom)) return 'bottom'

    return false
  }

  Affix.prototype.getPinnedOffset = function () {
    if (this.pinnedOffset) return this.pinnedOffset
    this.$element.removeClass(Affix.RESET).addClass('affix')
    var scrollTop = this.$target.scrollTop()
    var position  = this.$element.offset()
    return (this.pinnedOffset = position.top - scrollTop)
  }

  Affix.prototype.checkPositionWithEventLoop = function () {
    setTimeout($.proxy(this.checkPosition, this), 1)
  }

  Affix.prototype.checkPosition = function () {
    if (!this.$element.is(':visible')) return

    var height       = this.$element.height()
    var offset       = this.options.offset
    var offsetTop    = offset.top
    var offsetBottom = offset.bottom
    var scrollHeight = Math.max($(document).height(), $(document.body).height())

    if (typeof offset != 'object')         offsetBottom = offsetTop = offset
    if (typeof offsetTop == 'function')    offsetTop    = offset.top(this.$element)
    if (typeof offsetBottom == 'function') offsetBottom = offset.bottom(this.$element)

    var affix = this.getState(scrollHeight, height, offsetTop, offsetBottom)

    if (this.affixed != affix) {
      if (this.unpin != null) this.$element.css('top', '')

      var affixType = 'affix' + (affix ? '-' + affix : '')
      var e         = $.Event(affixType + '.bs.affix')

      this.$element.trigger(e)

      if (e.isDefaultPrevented()) return

      this.affixed = affix
      this.unpin = affix == 'bottom' ? this.getPinnedOffset() : null

      this.$element
        .removeClass(Affix.RESET)
        .addClass(affixType)
        .trigger(affixType.replace('affix', 'affixed') + '.bs.affix')
    }

    if (affix == 'bottom') {
      this.$element.offset({
        top: scrollHeight - height - offsetBottom
      })
    }
  }


  // AFFIX PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.affix')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.affix', (data = new Affix(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.affix

  $.fn.affix             = Plugin
  $.fn.affix.Constructor = Affix


  // AFFIX NO CONFLICT
  // =================

  $.fn.affix.noConflict = function () {
    $.fn.affix = old
    return this
  }


  // AFFIX DATA-API
  // ==============

  $(window).on('load', function () {
    $('[data-spy="affix"]').each(function () {
      var $spy = $(this)
      var data = $spy.data()

      data.offset = data.offset || {}

      if (data.offsetBottom != null) data.offset.bottom = data.offsetBottom
      if (data.offsetTop    != null) data.offset.top    = data.offsetTop

      Plugin.call($spy, data)
    })
  })

}(jQuery);
;
/* ========================================================================
 * Bootstrap: alert.js v3.3.7
 * http://getbootstrap.com/javascript/#alerts
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // ALERT CLASS DEFINITION
  // ======================

  var dismiss = '[data-dismiss="alert"]'
  var Alert   = function (el) {
    $(el).on('click', dismiss, this.close)
  }

  Alert.VERSION = '3.3.7'

  Alert.TRANSITION_DURATION = 150

  Alert.prototype.close = function (e) {
    var $this    = $(this)
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = $(selector === '#' ? [] : selector)

    if (e) e.preventDefault()

    if (!$parent.length) {
      $parent = $this.closest('.alert')
    }

    $parent.trigger(e = $.Event('close.bs.alert'))

    if (e.isDefaultPrevented()) return

    $parent.removeClass('in')

    function removeElement() {
      // detach from parent, fire event then clean up data
      $parent.detach().trigger('closed.bs.alert').remove()
    }

    $.support.transition && $parent.hasClass('fade') ?
      $parent
        .one('bsTransitionEnd', removeElement)
        .emulateTransitionEnd(Alert.TRANSITION_DURATION) :
      removeElement()
  }


  // ALERT PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.alert')

      if (!data) $this.data('bs.alert', (data = new Alert(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.alert

  $.fn.alert             = Plugin
  $.fn.alert.Constructor = Alert


  // ALERT NO CONFLICT
  // =================

  $.fn.alert.noConflict = function () {
    $.fn.alert = old
    return this
  }


  // ALERT DATA-API
  // ==============

  $(document).on('click.bs.alert.data-api', dismiss, Alert.prototype.close)

}(jQuery);
;
/* ========================================================================
 * Bootstrap: button.js v3.3.7
 * http://getbootstrap.com/javascript/#buttons
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // BUTTON PUBLIC CLASS DEFINITION
  // ==============================

  var Button = function (element, options) {
    this.$element  = $(element)
    this.options   = $.extend({}, Button.DEFAULTS, options)
    this.isLoading = false
  }

  Button.VERSION  = '3.3.7'

  Button.DEFAULTS = {
    loadingText: 'loading...'
  }

  Button.prototype.setState = function (state) {
    var d    = 'disabled'
    var $el  = this.$element
    var val  = $el.is('input') ? 'val' : 'html'
    var data = $el.data()

    state += 'Text'

    if (data.resetText == null) $el.data('resetText', $el[val]())

    // push to event loop to allow forms to submit
    setTimeout($.proxy(function () {
      $el[val](data[state] == null ? this.options[state] : data[state])

      if (state == 'loadingText') {
        this.isLoading = true
        $el.addClass(d).attr(d, d).prop(d, true)
      } else if (this.isLoading) {
        this.isLoading = false
        $el.removeClass(d).removeAttr(d).prop(d, false)
      }
    }, this), 0)
  }

  Button.prototype.toggle = function () {
    var changed = true
    var $parent = this.$element.closest('[data-toggle="buttons"]')

    if ($parent.length) {
      var $input = this.$element.find('input')
      if ($input.prop('type') == 'radio') {
        if ($input.prop('checked')) changed = false
        $parent.find('.active').removeClass('active')
        this.$element.addClass('active')
      } else if ($input.prop('type') == 'checkbox') {
        if (($input.prop('checked')) !== this.$element.hasClass('active')) changed = false
        this.$element.toggleClass('active')
      }
      $input.prop('checked', this.$element.hasClass('active'))
      if (changed) $input.trigger('change')
    } else {
      this.$element.attr('aria-pressed', !this.$element.hasClass('active'))
      this.$element.toggleClass('active')
    }
  }


  // BUTTON PLUGIN DEFINITION
  // ========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.button')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.button', (data = new Button(this, options)))

      if (option == 'toggle') data.toggle()
      else if (option) data.setState(option)
    })
  }

  var old = $.fn.button

  $.fn.button             = Plugin
  $.fn.button.Constructor = Button


  // BUTTON NO CONFLICT
  // ==================

  $.fn.button.noConflict = function () {
    $.fn.button = old
    return this
  }


  // BUTTON DATA-API
  // ===============

  $(document)
    .on('click.bs.button.data-api', '[data-toggle^="button"]', function (e) {
      var $btn = $(e.target).closest('.btn')
      Plugin.call($btn, 'toggle')
      if (!($(e.target).is('input[type="radio"], input[type="checkbox"]'))) {
        // Prevent double click on radios, and the double selections (so cancellation) on checkboxes
        e.preventDefault()
        // The target component still receive the focus
        if ($btn.is('input,button')) $btn.trigger('focus')
        else $btn.find('input:visible,button:visible').first().trigger('focus')
      }
    })
    .on('focus.bs.button.data-api blur.bs.button.data-api', '[data-toggle^="button"]', function (e) {
      $(e.target).closest('.btn').toggleClass('focus', /^focus(in)?$/.test(e.type))
    })

}(jQuery);
;
/* ========================================================================
 * Bootstrap: modal.js v3.3.7
 * http://getbootstrap.com/javascript/#modals
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // MODAL CLASS DEFINITION
  // ======================

  var Modal = function (element, options) {
    this.options             = options
    this.$body               = $(document.body)
    this.$element            = $(element)
    this.$dialog             = this.$element.find('.modal-dialog')
    this.$backdrop           = null
    this.isShown             = null
    this.originalBodyPad     = null
    this.scrollbarWidth      = 0
    this.ignoreBackdropClick = false

    if (this.options.remote) {
      this.$element
        .find('.modal-content')
        .load(this.options.remote, $.proxy(function () {
          this.$element.trigger('loaded.bs.modal')
        }, this))
    }
  }

  Modal.VERSION  = '3.3.7'

  Modal.TRANSITION_DURATION = 300
  Modal.BACKDROP_TRANSITION_DURATION = 150

  Modal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  }

  Modal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget)
  }

  Modal.prototype.show = function (_relatedTarget) {
    var that = this
    var e    = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.checkScrollbar()
    this.setScrollbar()
    this.$body.addClass('modal-open')

    this.escape()
    this.resize()

    this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

    this.$dialog.on('mousedown.dismiss.bs.modal', function () {
      that.$element.one('mouseup.dismiss.bs.modal', function (e) {
        if ($(e.target).is(that.$element)) that.ignoreBackdropClick = true
      })
    })

    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade')

      if (!that.$element.parent().length) {
        that.$element.appendTo(that.$body) // don't move modals dom position
      }

      that.$element
        .show()
        .scrollTop(0)

      that.adjustDialog()

      if (transition) {
        that.$element[0].offsetWidth // force reflow
      }

      that.$element.addClass('in')

      that.enforceFocus()

      var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

      transition ?
        that.$dialog // wait for modal to slide in
          .one('bsTransitionEnd', function () {
            that.$element.trigger('focus').trigger(e)
          })
          .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
        that.$element.trigger('focus').trigger(e)
    })
  }

  Modal.prototype.hide = function (e) {
    if (e) e.preventDefault()

    e = $.Event('hide.bs.modal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.escape()
    this.resize()

    $(document).off('focusin.bs.modal')

    this.$element
      .removeClass('in')
      .off('click.dismiss.bs.modal')
      .off('mouseup.dismiss.bs.modal')

    this.$dialog.off('mousedown.dismiss.bs.modal')

    $.support.transition && this.$element.hasClass('fade') ?
      this.$element
        .one('bsTransitionEnd', $.proxy(this.hideModal, this))
        .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
      this.hideModal()
  }

  Modal.prototype.enforceFocus = function () {
    $(document)
      .off('focusin.bs.modal') // guard against infinite focus loop
      .on('focusin.bs.modal', $.proxy(function (e) {
        if (document !== e.target &&
            this.$element[0] !== e.target &&
            !this.$element.has(e.target).length) {
          this.$element.trigger('focus')
        }
      }, this))
  }

  Modal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keydown.dismiss.bs.modal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keydown.dismiss.bs.modal')
    }
  }

  Modal.prototype.resize = function () {
    if (this.isShown) {
      $(window).on('resize.bs.modal', $.proxy(this.handleUpdate, this))
    } else {
      $(window).off('resize.bs.modal')
    }
  }

  Modal.prototype.hideModal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.$body.removeClass('modal-open')
      that.resetAdjustments()
      that.resetScrollbar()
      that.$element.trigger('hidden.bs.modal')
    })
  }

  Modal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove()
    this.$backdrop = null
  }

  Modal.prototype.backdrop = function (callback) {
    var that = this
    var animate = this.$element.hasClass('fade') ? 'fade' : ''

    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate

      this.$backdrop = $(document.createElement('div'))
        .addClass('modal-backdrop ' + animate)
        .appendTo(this.$body)

      this.$element.on('click.dismiss.bs.modal', $.proxy(function (e) {
        if (this.ignoreBackdropClick) {
          this.ignoreBackdropClick = false
          return
        }
        if (e.target !== e.currentTarget) return
        this.options.backdrop == 'static'
          ? this.$element[0].focus()
          : this.hide()
      }, this))

      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

      this.$backdrop.addClass('in')

      if (!callback) return

      doAnimate ?
        this.$backdrop
          .one('bsTransitionEnd', callback)
          .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
        callback()

    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in')

      var callbackRemove = function () {
        that.removeBackdrop()
        callback && callback()
      }
      $.support.transition && this.$element.hasClass('fade') ?
        this.$backdrop
          .one('bsTransitionEnd', callbackRemove)
          .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
        callbackRemove()

    } else if (callback) {
      callback()
    }
  }

  // these following methods are used to handle overflowing modals

  Modal.prototype.handleUpdate = function () {
    this.adjustDialog()
  }

  Modal.prototype.adjustDialog = function () {
    var modalIsOverflowing = this.$element[0].scrollHeight > document.documentElement.clientHeight

    this.$element.css({
      paddingLeft:  !this.bodyIsOverflowing && modalIsOverflowing ? this.scrollbarWidth : '',
      paddingRight: this.bodyIsOverflowing && !modalIsOverflowing ? this.scrollbarWidth : ''
    })
  }

  Modal.prototype.resetAdjustments = function () {
    this.$element.css({
      paddingLeft: '',
      paddingRight: ''
    })
  }

  Modal.prototype.checkScrollbar = function () {
    var fullWindowWidth = window.innerWidth
    if (!fullWindowWidth) { // workaround for missing window.innerWidth in IE8
      var documentElementRect = document.documentElement.getBoundingClientRect()
      fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left)
    }
    this.bodyIsOverflowing = document.body.clientWidth < fullWindowWidth
    this.scrollbarWidth = this.measureScrollbar()
  }

  Modal.prototype.setScrollbar = function () {
    var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
    this.originalBodyPad = document.body.style.paddingRight || ''
    if (this.bodyIsOverflowing) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
  }

  Modal.prototype.resetScrollbar = function () {
    this.$body.css('padding-right', this.originalBodyPad)
  }

  Modal.prototype.measureScrollbar = function () { // thx walsh
    var scrollDiv = document.createElement('div')
    scrollDiv.className = 'modal-scrollbar-measure'
    this.$body.append(scrollDiv)
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
    this.$body[0].removeChild(scrollDiv)
    return scrollbarWidth
  }


  // MODAL PLUGIN DEFINITION
  // =======================

  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.modal')
      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
      if (typeof option == 'string') data[option](_relatedTarget)
      else if (options.show) data.show(_relatedTarget)
    })
  }

  var old = $.fn.modal

  $.fn.modal             = Plugin
  $.fn.modal.Constructor = Modal


  // MODAL NO CONFLICT
  // =================

  $.fn.modal.noConflict = function () {
    $.fn.modal = old
    return this
  }


  // MODAL DATA-API
  // ==============

  $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
    var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target.one('show.bs.modal', function (showEvent) {
      if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
      $target.one('hidden.bs.modal', function () {
        $this.is(':visible') && $this.trigger('focus')
      })
    })
    Plugin.call($target, option, this)
  })

}(jQuery);
;
/* ========================================================================
 * Bootstrap: tooltip.js v3.3.7
 * http://getbootstrap.com/javascript/#tooltip
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // TOOLTIP PUBLIC CLASS DEFINITION
  // ===============================

  var Tooltip = function (element, options) {
    this.type       = null
    this.options    = null
    this.enabled    = null
    this.timeout    = null
    this.hoverState = null
    this.$element   = null
    this.inState    = null

    this.init('tooltip', element, options)
  }

  Tooltip.VERSION  = '3.3.7'

  Tooltip.TRANSITION_DURATION = 150

  Tooltip.DEFAULTS = {
    animation: true,
    placement: 'top',
    selector: false,
    template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
    trigger: 'hover focus',
    title: '',
    delay: 0,
    html: false,
    container: false,
    viewport: {
      selector: 'body',
      padding: 0
    }
  }

  Tooltip.prototype.init = function (type, element, options) {
    this.enabled   = true
    this.type      = type
    this.$element  = $(element)
    this.options   = this.getOptions(options)
    this.$viewport = this.options.viewport && $($.isFunction(this.options.viewport) ? this.options.viewport.call(this, this.$element) : (this.options.viewport.selector || this.options.viewport))
    this.inState   = { click: false, hover: false, focus: false }

    if (this.$element[0] instanceof document.constructor && !this.options.selector) {
      throw new Error('`selector` option must be specified when initializing ' + this.type + ' on the window.document object!')
    }

    var triggers = this.options.trigger.split(' ')

    for (var i = triggers.length; i--;) {
      var trigger = triggers[i]

      if (trigger == 'click') {
        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
      } else if (trigger != 'manual') {
        var eventIn  = trigger == 'hover' ? 'mouseenter' : 'focusin'
        var eventOut = trigger == 'hover' ? 'mouseleave' : 'focusout'

        this.$element.on(eventIn  + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
      }
    }

    this.options.selector ?
      (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
      this.fixTitle()
  }

  Tooltip.prototype.getDefaults = function () {
    return Tooltip.DEFAULTS
  }

  Tooltip.prototype.getOptions = function (options) {
    options = $.extend({}, this.getDefaults(), this.$element.data(), options)

    if (options.delay && typeof options.delay == 'number') {
      options.delay = {
        show: options.delay,
        hide: options.delay
      }
    }

    return options
  }

  Tooltip.prototype.getDelegateOptions = function () {
    var options  = {}
    var defaults = this.getDefaults()

    this._options && $.each(this._options, function (key, value) {
      if (defaults[key] != value) options[key] = value
    })

    return options
  }

  Tooltip.prototype.enter = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type)

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    if (obj instanceof $.Event) {
      self.inState[obj.type == 'focusin' ? 'focus' : 'hover'] = true
    }

    if (self.tip().hasClass('in') || self.hoverState == 'in') {
      self.hoverState = 'in'
      return
    }

    clearTimeout(self.timeout)

    self.hoverState = 'in'

    if (!self.options.delay || !self.options.delay.show) return self.show()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'in') self.show()
    }, self.options.delay.show)
  }

  Tooltip.prototype.isInStateTrue = function () {
    for (var key in this.inState) {
      if (this.inState[key]) return true
    }

    return false
  }

  Tooltip.prototype.leave = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type)

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    if (obj instanceof $.Event) {
      self.inState[obj.type == 'focusout' ? 'focus' : 'hover'] = false
    }

    if (self.isInStateTrue()) return

    clearTimeout(self.timeout)

    self.hoverState = 'out'

    if (!self.options.delay || !self.options.delay.hide) return self.hide()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'out') self.hide()
    }, self.options.delay.hide)
  }

  Tooltip.prototype.show = function () {
    var e = $.Event('show.bs.' + this.type)

    if (this.hasContent() && this.enabled) {
      this.$element.trigger(e)

      var inDom = $.contains(this.$element[0].ownerDocument.documentElement, this.$element[0])
      if (e.isDefaultPrevented() || !inDom) return
      var that = this

      var $tip = this.tip()

      var tipId = this.getUID(this.type)

      this.setContent()
      $tip.attr('id', tipId)
      this.$element.attr('aria-describedby', tipId)

      if (this.options.animation) $tip.addClass('fade')

      var placement = typeof this.options.placement == 'function' ?
        this.options.placement.call(this, $tip[0], this.$element[0]) :
        this.options.placement

      var autoToken = /\s?auto?\s?/i
      var autoPlace = autoToken.test(placement)
      if (autoPlace) placement = placement.replace(autoToken, '') || 'top'

      $tip
        .detach()
        .css({ top: 0, left: 0, display: 'block' })
        .addClass(placement)
        .data('bs.' + this.type, this)

      this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)
      this.$element.trigger('inserted.bs.' + this.type)

      var pos          = this.getPosition()
      var actualWidth  = $tip[0].offsetWidth
      var actualHeight = $tip[0].offsetHeight

      if (autoPlace) {
        var orgPlacement = placement
        var viewportDim = this.getPosition(this.$viewport)

        placement = placement == 'bottom' && pos.bottom + actualHeight > viewportDim.bottom ? 'top'    :
                    placement == 'top'    && pos.top    - actualHeight < viewportDim.top    ? 'bottom' :
                    placement == 'right'  && pos.right  + actualWidth  > viewportDim.width  ? 'left'   :
                    placement == 'left'   && pos.left   - actualWidth  < viewportDim.left   ? 'right'  :
                    placement

        $tip
          .removeClass(orgPlacement)
          .addClass(placement)
      }

      var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight)

      this.applyPlacement(calculatedOffset, placement)

      var complete = function () {
        var prevHoverState = that.hoverState
        that.$element.trigger('shown.bs.' + that.type)
        that.hoverState = null

        if (prevHoverState == 'out') that.leave(that)
      }

      $.support.transition && this.$tip.hasClass('fade') ?
        $tip
          .one('bsTransitionEnd', complete)
          .emulateTransitionEnd(Tooltip.TRANSITION_DURATION) :
        complete()
    }
  }

  Tooltip.prototype.applyPlacement = function (offset, placement) {
    var $tip   = this.tip()
    var width  = $tip[0].offsetWidth
    var height = $tip[0].offsetHeight

    // manually read margins because getBoundingClientRect includes difference
    var marginTop = parseInt($tip.css('margin-top'), 10)
    var marginLeft = parseInt($tip.css('margin-left'), 10)

    // we must check for NaN for ie 8/9
    if (isNaN(marginTop))  marginTop  = 0
    if (isNaN(marginLeft)) marginLeft = 0

    offset.top  += marginTop
    offset.left += marginLeft

    // $.fn.offset doesn't round pixel values
    // so we use setOffset directly with our own function B-0
    $.offset.setOffset($tip[0], $.extend({
      using: function (props) {
        $tip.css({
          top: Math.round(props.top),
          left: Math.round(props.left)
        })
      }
    }, offset), 0)

    $tip.addClass('in')

    // check to see if placing tip in new offset caused the tip to resize itself
    var actualWidth  = $tip[0].offsetWidth
    var actualHeight = $tip[0].offsetHeight

    if (placement == 'top' && actualHeight != height) {
      offset.top = offset.top + height - actualHeight
    }

    var delta = this.getViewportAdjustedDelta(placement, offset, actualWidth, actualHeight)

    if (delta.left) offset.left += delta.left
    else offset.top += delta.top

    var isVertical          = /top|bottom/.test(placement)
    var arrowDelta          = isVertical ? delta.left * 2 - width + actualWidth : delta.top * 2 - height + actualHeight
    var arrowOffsetPosition = isVertical ? 'offsetWidth' : 'offsetHeight'

    $tip.offset(offset)
    this.replaceArrow(arrowDelta, $tip[0][arrowOffsetPosition], isVertical)
  }

  Tooltip.prototype.replaceArrow = function (delta, dimension, isVertical) {
    this.arrow()
      .css(isVertical ? 'left' : 'top', 50 * (1 - delta / dimension) + '%')
      .css(isVertical ? 'top' : 'left', '')
  }

  Tooltip.prototype.setContent = function () {
    var $tip  = this.tip()
    var title = this.getTitle()

    $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
    $tip.removeClass('fade in top bottom left right')
  }

  Tooltip.prototype.hide = function (callback) {
    var that = this
    var $tip = $(this.$tip)
    var e    = $.Event('hide.bs.' + this.type)

    function complete() {
      if (that.hoverState != 'in') $tip.detach()
      if (that.$element) { // TODO: Check whether guarding this code with this `if` is really necessary.
        that.$element
          .removeAttr('aria-describedby')
          .trigger('hidden.bs.' + that.type)
      }
      callback && callback()
    }

    this.$element.trigger(e)

    if (e.isDefaultPrevented()) return

    $tip.removeClass('in')

    $.support.transition && $tip.hasClass('fade') ?
      $tip
        .one('bsTransitionEnd', complete)
        .emulateTransitionEnd(Tooltip.TRANSITION_DURATION) :
      complete()

    this.hoverState = null

    return this
  }

  Tooltip.prototype.fixTitle = function () {
    var $e = this.$element
    if ($e.attr('title') || typeof $e.attr('data-original-title') != 'string') {
      $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
    }
  }

  Tooltip.prototype.hasContent = function () {
    return this.getTitle()
  }

  Tooltip.prototype.getPosition = function ($element) {
    $element   = $element || this.$element

    var el     = $element[0]
    var isBody = el.tagName == 'BODY'

    var elRect    = el.getBoundingClientRect()
    if (elRect.width == null) {
      // width and height are missing in IE8, so compute them manually; see https://github.com/twbs/bootstrap/issues/14093
      elRect = $.extend({}, elRect, { width: elRect.right - elRect.left, height: elRect.bottom - elRect.top })
    }
    var isSvg = window.SVGElement && el instanceof window.SVGElement
    // Avoid using $.offset() on SVGs since it gives incorrect results in jQuery 3.
    // See https://github.com/twbs/bootstrap/issues/20280
    var elOffset  = isBody ? { top: 0, left: 0 } : (isSvg ? null : $element.offset())
    var scroll    = { scroll: isBody ? document.documentElement.scrollTop || document.body.scrollTop : $element.scrollTop() }
    var outerDims = isBody ? { width: $(window).width(), height: $(window).height() } : null

    return $.extend({}, elRect, scroll, outerDims, elOffset)
  }

  Tooltip.prototype.getCalculatedOffset = function (placement, pos, actualWidth, actualHeight) {
    return placement == 'bottom' ? { top: pos.top + pos.height,   left: pos.left + pos.width / 2 - actualWidth / 2 } :
           placement == 'top'    ? { top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2 } :
           placement == 'left'   ? { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth } :
        /* placement == 'right' */ { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width }

  }

  Tooltip.prototype.getViewportAdjustedDelta = function (placement, pos, actualWidth, actualHeight) {
    var delta = { top: 0, left: 0 }
    if (!this.$viewport) return delta

    var viewportPadding = this.options.viewport && this.options.viewport.padding || 0
    var viewportDimensions = this.getPosition(this.$viewport)

    if (/right|left/.test(placement)) {
      var topEdgeOffset    = pos.top - viewportPadding - viewportDimensions.scroll
      var bottomEdgeOffset = pos.top + viewportPadding - viewportDimensions.scroll + actualHeight
      if (topEdgeOffset < viewportDimensions.top) { // top overflow
        delta.top = viewportDimensions.top - topEdgeOffset
      } else if (bottomEdgeOffset > viewportDimensions.top + viewportDimensions.height) { // bottom overflow
        delta.top = viewportDimensions.top + viewportDimensions.height - bottomEdgeOffset
      }
    } else {
      var leftEdgeOffset  = pos.left - viewportPadding
      var rightEdgeOffset = pos.left + viewportPadding + actualWidth
      if (leftEdgeOffset < viewportDimensions.left) { // left overflow
        delta.left = viewportDimensions.left - leftEdgeOffset
      } else if (rightEdgeOffset > viewportDimensions.right) { // right overflow
        delta.left = viewportDimensions.left + viewportDimensions.width - rightEdgeOffset
      }
    }

    return delta
  }

  Tooltip.prototype.getTitle = function () {
    var title
    var $e = this.$element
    var o  = this.options

    title = $e.attr('data-original-title')
      || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

    return title
  }

  Tooltip.prototype.getUID = function (prefix) {
    do prefix += ~~(Math.random() * 1000000)
    while (document.getElementById(prefix))
    return prefix
  }

  Tooltip.prototype.tip = function () {
    if (!this.$tip) {
      this.$tip = $(this.options.template)
      if (this.$tip.length != 1) {
        throw new Error(this.type + ' `template` option must consist of exactly 1 top-level element!')
      }
    }
    return this.$tip
  }

  Tooltip.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find('.tooltip-arrow'))
  }

  Tooltip.prototype.enable = function () {
    this.enabled = true
  }

  Tooltip.prototype.disable = function () {
    this.enabled = false
  }

  Tooltip.prototype.toggleEnabled = function () {
    this.enabled = !this.enabled
  }

  Tooltip.prototype.toggle = function (e) {
    var self = this
    if (e) {
      self = $(e.currentTarget).data('bs.' + this.type)
      if (!self) {
        self = new this.constructor(e.currentTarget, this.getDelegateOptions())
        $(e.currentTarget).data('bs.' + this.type, self)
      }
    }

    if (e) {
      self.inState.click = !self.inState.click
      if (self.isInStateTrue()) self.enter(self)
      else self.leave(self)
    } else {
      self.tip().hasClass('in') ? self.leave(self) : self.enter(self)
    }
  }

  Tooltip.prototype.destroy = function () {
    var that = this
    clearTimeout(this.timeout)
    this.hide(function () {
      that.$element.off('.' + that.type).removeData('bs.' + that.type)
      if (that.$tip) {
        that.$tip.detach()
      }
      that.$tip = null
      that.$arrow = null
      that.$viewport = null
      that.$element = null
    })
  }


  // TOOLTIP PLUGIN DEFINITION
  // =========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.tooltip')
      var options = typeof option == 'object' && option

      if (!data && /destroy|hide/.test(option)) return
      if (!data) $this.data('bs.tooltip', (data = new Tooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.tooltip

  $.fn.tooltip             = Plugin
  $.fn.tooltip.Constructor = Tooltip


  // TOOLTIP NO CONFLICT
  // ===================

  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old
    return this
  }

}(jQuery);
;
/* ========================================================================
 * Bootstrap: transition.js v3.3.7
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
  // ============================================================

  function transitionEnd() {
    var el = document.createElement('bootstrap')

    var transEndEventNames = {
      WebkitTransition : 'webkitTransitionEnd',
      MozTransition    : 'transitionend',
      OTransition      : 'oTransitionEnd otransitionend',
      transition       : 'transitionend'
    }

    for (var name in transEndEventNames) {
      if (el.style[name] !== undefined) {
        return { end: transEndEventNames[name] }
      }
    }

    return false // explicit for ie8 (  ._.)
  }

  // http://blog.alexmaccaw.com/css-transitions
  $.fn.emulateTransitionEnd = function (duration) {
    var called = false
    var $el = this
    $(this).one('bsTransitionEnd', function () { called = true })
    var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
    setTimeout(callback, duration)
    return this
  }

  $(function () {
    $.support.transition = transitionEnd()

    if (!$.support.transition) return

    $.event.special.bsTransitionEnd = {
      bindType: $.support.transition.end,
      delegateType: $.support.transition.end,
      handle: function (e) {
        if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
      }
    }
  })

}(jQuery);
;
var jsvat = (function() {

  'use strict'

  function Result(vat, isValid, country) {
    this.value = vat || null
    this.isValid = !!isValid

    if (country) {
      this.country = {
        name: country.name,
        isoCode: {
          short: country.codes[0],
          long: country.codes[1],
          numeric: country.codes[2]
        }
      }
    }
  }

  function removeExtraChars(vat) {
    vat = vat || ''
    return vat.toString().toUpperCase().replace(/(\s|-|\.)+/g, '')
  }

  function isValEqToCode(val, codes) {
    return (val === codes[0] || val === codes[1] || val === codes[2])
  }

  function isInList(list, country) {
    if (!list) return false

    for (var i = 0; i < list.length; i++) {
      var val = list[i].toUpperCase()
      if (val === country.name.toUpperCase()) return true
      if (isValEqToCode(val, country.codes)) return true
    }

    return false
  }

  function isBlocked(country, blocked, allowed) {
    var isBlocked = isInList(blocked, country)
    if (isBlocked) return true
    var isAllowed = isInList(allowed, country)
    return allowed.length > 0 && !isAllowed
  }

  function getCountry(vat, countries) {
    for (var k in countries) {
      if (countries.hasOwnProperty(k)) {
        var regexpValidRes = isVatValidToRegexp(vat, countries[k].rules.regex)
        if (regexpValidRes.isValid) return countries[k]
      }
    }

    return null
  }

  function isVatValidToRegexp(vat, regexArr) {
    for (var i = 0; i < regexArr.length; i++) {
      var regex = regexArr[i]
      var isValid = regex.test(vat)
      if (isValid) return {
        isValid: true,
        regex: regex
      }
    }

    return {
      isValid: false
    }
  }

  function isVatMathValid(vat, country) {
    return country.calcFn(vat)
  }

  function isVatValid(vat, country) {
    var regexpValidRes = isVatValidToRegexp(vat, country.rules.regex)
    if (!regexpValidRes.isValid) return false
    return isVatMathValid(regexpValidRes.regex.exec(vat)[2], country)
  }

  // eslint-disable-next-line no-unused-vars
  var exports = {
    blocked: [],
    allowed: [],
    countries: {},
    checkVAT: function(vat) {
      if (!vat) throw new Error('VAT should be specified')
      var cleanVAT = removeExtraChars(vat)
      var result = new Result(cleanVAT)

      var country = getCountry(cleanVAT, this.countries)
      if (!country) return result
      if (isBlocked(country, this.blocked, this.allowed)) return new Result(cleanVAT, false, country)

      var isValid = isVatValid(cleanVAT, country)
      if (isValid) return new Result(cleanVAT, isValid, country)

      return result
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.austria = {
    name: 'Austria',
    codes: ['AT', 'AUT', '040'],
    calcFn: function(vat) {
      var total = 0
      var temp

      for (var i = 0; i < 7; i++) {
        temp = vat.charAt(i) * this.rules.multipliers[i]

        if (temp > 9) {
          total += Math.floor(temp / 10) + temp % 10
        } else {
          total += temp
        }
      }

      total = 10 - (total + 4) % 10
      if (total === 10) total = 0

      return total === +vat.slice(7, 8)
    },
    rules: {
      multipliers: [1, 2, 1, 2, 1, 2, 1],
      regex: [/^(AT)U(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.belgium = {
    name: 'Belgium',
    codes: ['BE', 'BEL', '056'],
    calcFn: function(vat) {
      if (vat.length === 9) {
        vat = '0' + vat
      }

      if (+vat.slice(1, 2) === 0) return false

      var check = (97 - +vat.slice(0, 8) % 97)
      return check === +vat.slice(8, 10)
    },
    rules: {
      regex: [/^(BE)(0?\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.bulgaria = {
    name: 'Bulgaria',
    codes: ['BG', 'BGR', '100'],
    calcFn: function(vat) {
      function _increase(value, vat, from, to, incr) {
        for (var i = from; i < to; i++) {
          value += +vat.charAt(i) * (i + incr)
        }
        return value
      }

      function _increase2(value, vat, from, to, multipliers) {
        for (var i = from; i < to; i++) {
          value += +vat.charAt(i) * multipliers[i]
        }
        return value
      }

      function _checkNineLengthVat(vat) {
        var total
        var temp = 0
        var expect = +vat.slice(8)

        temp = _increase(temp, vat, 0, 8, 1)

        total = temp % 11
        if (total !== 10) {
          return total === expect
        }

        temp = _increase(0, vat, 0, 8, 3)

        total = temp % 11
        if (total === 10) total = 0

        return total === expect
      }

      function _isPhysicalPerson(vat, rules) {
        // 10 digit VAT code - see if it relates to a standard physical person
        if ((/^\d\d[0-5]\d[0-3]\d\d{4}$/).test(vat)) {
          // Check month
          var month = +vat.slice(2, 4)
          if ((month > 0 && month < 13) || (month > 20 && month < 33) || (month > 40 && month < 53)) {
            var total = _increase2(0, vat, 0, 9, rules.multipliers.physical)
            // Establish check digit.
            total = total % 11
            if (total === 10) total = 0
            // Check to see if the check digit given is correct, If not, try next type of person
            if (total === +vat.substr(9, 1)) return true
          }
        }

        return false
      }

      function _isForeigner(vat, rules) {
        // Extract the next digit and multiply by the counter.
        var total = _increase2(0, vat, 0, 9, rules.multipliers.foreigner)

        // Check to see if the check digit given is correct, If not, try next type of person
        if (total % 10 === +vat.substr(9, 1)) {
          return true
        }
      }

      function _miscellaneousVAT(vat, rules) {
        // Finally, if not yet identified, see if it conforms to a miscellaneous VAT number
        var total = _increase2(0, vat, 0, 9, rules.multipliers.miscellaneous)

        // Establish check digit.
        total = 11 - total % 11
        if (total === 10) return false
        if (total === 11) total = 0

        // Check to see if the check digit given is correct, If not, we have an error with the VAT number
        var expect = +vat.substr(9, 1)
        return total === expect
      }

      if (vat.length === 9) {
        return _checkNineLengthVat(vat)
      } else {
        return _isPhysicalPerson(vat, this.rules) || _isForeigner(vat, this.rules) || _miscellaneousVAT(vat, this.rules)
      }
    },
    rules: {
      multipliers: {
        physical: [2, 4, 8, 5, 10, 9, 7, 3, 6],
        foreigner: [21, 19, 17, 13, 11, 9, 7, 3, 1],
        miscellaneous: [4, 3, 2, 7, 6, 5, 4, 3, 2]
      },
      regex: [/^(BG)(\d{9,10})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.croatia = {
    name: 'Croatia',
    codes: ['HR', 'HRV', '191'],
    calcFn: function(vat) {
      var expect

      // Checks the check digits of a Croatian VAT number using ISO 7064, MOD 11-10 for check digit.
      var product = 10
      var sum = 0

      for (var i = 0; i < 10; i++) {
        // Extract the next digit and implement the algorithm
        sum = (+vat.charAt(i) + product) % 10
        if (sum === 0) {
          sum = 10
        }

        product = (2 * sum) % 11
      }

      // Now check that we have the right check digit
      expect = +vat.slice(10, 11)
      return (product + expect) % 10 === 1
    },
    rules: {
      regex: [/^(HR)(\d{11})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.cyprus = {
    name: 'Cyprus',
    codes: ['CY', 'CYP', '196'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Not allowed to start with '12'
      if (+vat.slice(0, 2) === 12) return false

      // Extract the next digit and multiply by the counter.

      for (var i = 0; i < 8; i++) {
        var temp = +vat.charAt(i)
        if (i % 2 === 0) {
          switch (temp) {
            case 0:
              temp = 1
              break
            case 1:
              temp = 0
              break
            case 2:
              temp = 5
              break
            case 3:
              temp = 7
              break
            case 4:
              temp = 9
              break
            default:
              temp = temp * 2 + 3
          }
        }
        total += temp
      }

      // Establish check digit using modulus 26, and translate to char. equivalent.
      total = total % 26
      total = String.fromCharCode(total + 65)

      // Check to see if the check digit given is correct
      expect = vat.substr(8, 1)
      return total === expect
    },
    rules: {
      regex: [/^(CY)([0-59]\d{7}[A-Z])$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.czech_republic = {
    name: 'Czech Republic',
    codes: ['CZ', 'CZE', '203'],
    calcFn: function(vat) {
      function _isLegalEntities(vat, rules) {
        var total = 0

        if (rules.additional[0].test(vat)) {
          // Extract the next digit and multiply by the counter.
          for (var i = 0; i < 7; i++) {
            total += +vat.charAt(i) * rules.multipliers[i]
          }

          // Establish check digit.
          total = 11 - total % 11
          if (total === 10) total = 0
          if (total === 11) total = 1

          // Compare it with the last character of the VAT number. If it's the same, then it's valid.
          var expect = +vat.slice(7, 8)
          return total === expect
        }

        return false
      }

      function _isIndividualType1(vat, rules) {
        if (rules.additional[1].test(vat)) {
          var temp = +vat.slice(0, 2)

          if (temp > 62) {
            return false
          } else {
            return true
          }
        }
      }

      function _isIndividualType2(vat, rules) {
        var total = 0

        if (rules.additional[2].test(vat)) {
          // Extract the next digit and multiply by the counter.
          for (var j = 0; j < 7; j++) {
            total += +vat.charAt(j + 1) * rules.multipliers[j]
          }

          // Establish check digit.
          total = 11 - total % 11
          if (total === 10) total = 0
          if (total === 11) total = 1

          // Convert calculated check digit according to a lookup table
          var expect = +vat.slice(8, 9)
          return rules.lookup[total - 1] === expect
        }

        return false
      }

      function _isIndividualType3(vat, rules) {
        if (rules.additional[3].test(vat)) {
          var temp = +vat.slice(0, 2) + vat.slice(2, 4) + vat.slice(4, 6) + vat.slice(6, 8) + vat.slice(8)
          var expect = +vat % 11 === 0
          return !!(temp % 11 === 0 && expect)
        }

        return false
      }

      if (_isLegalEntities(vat, this.rules)) return true
      if (_isIndividualType2(vat, this.rules)) return true
      if (_isIndividualType3(vat, this.rules)) return true
      if (_isIndividualType1(vat, this.rules)) return true

      return false
    },
    rules: {
      multipliers: [8, 7, 6, 5, 4, 3, 2],
      lookup: [8, 7, 6, 5, 4, 3, 2, 1, 0, 9, 10],
      regex: [/^(CZ)(\d{8,10})(\d{3})?$/],
      additional: [
        /^\d{8}$/,
        /^[0-5][0-9][0|1|5|6]\d[0-3]\d\d{3}$/,
        /^6\d{8}$/,
        /^\d{2}[0-3|5-8]\d[0-3]\d\d{4}$/
      ]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.denmark = {
    name: 'Denmark',
    codes: ['DK', 'DNK', '208'],
    calcFn: function(vat) {
      var total = 0

      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      return total % 11 === 0
    },
    rules: {
      multipliers: [2, 7, 6, 5, 4, 3, 2, 1],
      regex: [/^(DK)(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.estonia = {
    name: 'Estonia',
    codes: ['EE', 'EST', '233'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits using modulus 10.
      total = 10 - total % 10
      if (total === 10) total = 0

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(8, 9)
      return total === expect
    },
    rules: {
      multipliers: [3, 7, 1, 3, 7, 1, 3, 7],
      regex: [/^(EE)(10\d{7})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.europe = {
    name: 'Europe',
    codes: ['EU', 'EUR', '000'], // TODO (S.Panfilov) that's not a real codes
    calcFn: function() {
      // We know little about EU numbers apart from the fact that the first 3 digits represent the
      // country, and that there are nine digits in total.
      return true
    },
    rules: {
      regex: [/^(EU)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.finland = {
    name: 'Finland',
    codes: ['FI', 'FIN', '246'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) total += +vat.charAt(i) * this.rules.multipliers[i]

      // Establish check digit.
      total = 11 - total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(7, 8)
      return total === expect
    },
    rules: {
      multipliers: [7, 9, 10, 5, 8, 4, 2],
      regex: [/^(FI)(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.france = {
    name: 'France',
    codes: ['FR', 'FRA', '250'],
    calcFn: function(vat) {
      var total
      var expect

      // Checks the check digits of a French VAT number.
      if (!(/^\d{11}$/).test(vat)) {
        return true
      }

      // Extract the last nine digits as an integer.
      total = +vat.substring(2)

      // Establish check digit.
      total = (total * 100 + 12) % 97

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(0, 2)
      return total === expect
    },
    rules: {
      regex: [
        /^(FR)(\d{11})$/,
        /^(FR)([A-HJ-NP-Z]\d{10})$/,
        /^(FR)(\d[A-HJ-NP-Z]\d{9})$/,
        /^(FR)([A-HJ-NP-Z]{2}\d{9})$/
      ]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.germany = {
    name: 'Germany',
    codes: ['DE', 'DEU', '276'],
    calcFn: function(vat) {
      // Checks the check digits of a German VAT number.
      var product = 10
      var sum = 0
      var checkDigit = 0
      var expect

      for (var i = 0; i < 8; i++) {
        // Extract the next digit and implement peculiar algorithm!.
        sum = (+vat.charAt(i) + product) % 10
        if (sum === 0) {
          sum = 10
        }
        product = (2 * sum) % 11
      }

      // Establish check digit.
      if (11 - product === 10) {
        checkDigit = 0
      } else {
        checkDigit = 11 - product
      }

      // Compare it with the last two characters of the VAT number. If the same, then it is a valid
      // check digit.
      expect = +vat.slice(8, 9)
      return checkDigit === expect
    },
    rules: {
      regex: [/^(DE)([1-9]\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.greece = {
    name: 'Greece',
    codes: ['GR', 'GRC', '300'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // eight character numbers should be prefixed with an 0.
      if (vat.length === 8) {
        vat = '0' + vat
      }

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digit.
      total = total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(8, 9)
      return total === expect
    },
    rules: {
      multipliers: [
        256,
        128,
        64,
        32,
        16,
        8,
        4,
        2
      ],
      regex: [/^(EL)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.hungary = {
    name: 'Hungary',
    codes: ['HU', 'HUN', '348'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digit.
      total = 10 - total % 10
      if (total === 10) total = 0

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(7, 8)
      return total === expect
    },
    rules: {
      multipliers: [
        9,
        7,
        3,
        1,
        9,
        7,
        3
      ],
      regex: [/^(HU)(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.ireland = {
    name: 'Ireland',
    codes: ['IE', 'IRL', '372'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // If the code is type 1 format, we need to convert it to the new before performing the validation.
      if (this.rules.typeFormats.first.test(vat)) {
        vat = '0' + vat.substring(2, 7) + vat.substring(0, 1) + vat.substring(7, 8)
      }

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // If the number is type 3 then we need to include the trailing A or H in the calculation
      if (this.rules.typeFormats.third.test(vat)) {
        // Add in a multiplier for the character A (1*9=9) or H (8*9=72)
        if (vat.charAt(8) === 'H') {
          total += 72
        } else {
          total += 9
        }
      }

      // Establish check digit using modulus 23, and translate to char. equivalent.
      total = total % 23
      if (total === 0) {
        total = 'W'
      } else {
        total = String.fromCharCode(total + 64)
      }

      // Compare it with the eighth character of the VAT number. If it's the same, then it's valid.
      expect = vat.slice(7, 8)
      return total === expect
    },
    rules: {
      multipliers: [8, 7, 6, 5, 4, 3, 2],
      typeFormats: {
        first: /^\d[A-Z*+]/,
        third: /^\d{7}[A-Z][AH]$/
      },
      regex: [
        /^(IE)(\d{7}[A-W])$/,
        /^(IE)([7-9][A-Z*+)]\d{5}[A-W])$/,
        /^(IE)(\d{7}[A-W][AH])$/
      ]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.italy = {
    name: 'Italy',
    codes: ['IT', 'ITA', '380'],
    calcFn: function(vat) {
      var total = 0
      var temp
      var expect

      // The last three digits are the issuing office, and cannot exceed more 201, unless 999 or 888
      if (+vat.slice(0, 7) === 0) {
        return false
      }

      temp = +vat.slice(7, 10)
      if ((temp < 1) || (temp > 201) && temp !== 999 && temp !== 888) {
        return false
      }

      // Extract the next digit and multiply by the appropriate
      for (var i = 0; i < 10; i++) {
        temp = +vat.charAt(i) * this.rules.multipliers[i]
        if (temp > 9)
          total += Math.floor(temp / 10) + temp % 10
        else
          total += temp
      }

      // Establish check digit.
      total = 10 - total % 10
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(10, 11)
      return total === expect
    },
    rules: {
      multipliers: [1, 2, 1, 2, 1, 2, 1, 2, 1, 2],
      regex: [/^(IT)(\d{11})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.latvia = {
    name: 'Latvia',
    codes: ['LV', 'LVA', '428'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Differentiate between legal entities and natural bodies. For the latter we simply check that
      // the first six digits correspond to valid DDMMYY dates.
      if ((/^[0-3]/).test(vat)) {
        return !!(/^[0-3][0-9][0-1][0-9]/).test(vat)
      } else {
        // Extract the next digit and multiply by the counter.
        for (var i = 0; i < 10; i++) {
          total += +vat.charAt(i) * this.rules.multipliers[i]
        }

        // Establish check digits by getting modulus 11.
        if (total % 11 === 4 && vat[0] === 9) total = total - 45

        if (total % 11 === 4) {
          total = 4 - total % 11
        } else if (total % 11 > 4) {
          total = 14 - total % 11
        } else if (total % 11 < 4) {
          total = 3 - total % 11
        }

        // Compare it with the last character of the VAT number. If it's the same, then it's valid.
        expect = +vat.slice(10, 11)
        return total === expect
      }
    },
    rules: {
      multipliers: [9, 1, 4, 8, 3, 10, 2, 5, 7, 6],
      regex: [/^(LV)(\d{11})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.lithuania = {
    name: 'Lithuania',
    codes: ['LT', 'LTU', '440'],
    calcFn: function(vat) {
      function _extractDigit(vat, multiplier, key) {
        return +vat.charAt(key) * multiplier[key]
      }

      function _doubleCheckCalculation(vat, total, rules) {
        if (total % 11 === 10) {
          total = 0
          for (var i = 0; i < 8; i++) {
            total += _extractDigit(vat, rules.multipliers.short, i)
          }
        }

        return total
      }

      function extractDigit(vat, total) {
        for (var i = 0; i < 8; i++) {
          total += +vat.charAt(i) * (i + 1)
        }
        return total
      }

      function checkDigit(total) {
        total = total % 11
        if (total === 10) {
          total = 0
        }

        return total
      }

      function _check9DigitVat(vat, rules) {
        // 9 character VAT numbers are for legal persons
        var total = 0
        if (vat.length === 9) {
          // 8th character must be one
          if (!(/^\d{7}1/).test(vat)) return false

          // Extract the next digit and multiply by the counter+1.
          total = extractDigit(vat, total)

          // Can have a double check digit calculation!
          total = _doubleCheckCalculation(vat, total, rules)

          // Establish check digit.
          total = checkDigit(total)

          // Compare it with the last character of the VAT number. If it's the same, then it's valid.
          var expect = +vat.slice(8, 9)
          return total === expect
        }
        return false
      }

      function extractDigit12(vat, total, rules) {
        for (var k = 0; k < 11; k++) {
          total += _extractDigit(vat, rules.multipliers.med, k)
        }
        return total
      }

      function _doubleCheckCalculation12(vat, total, rules) {
        if (total % 11 === 10) {
          total = 0
          for (var l = 0; l < 11; l++) {
            total += _extractDigit(vat, rules.multipliers.alt, l)
          }
        }

        return total
      }

      function _check12DigitVat(vat, rules) {
        var total = 0

        // 12 character VAT numbers are for temporarily registered taxpayers
        if (vat.length === 12) {
          // 11th character must be one
          if (!(rules.check).test(vat)) return false

          // Extract the next digit and multiply by the counter+1.
          total = extractDigit12(vat, total, rules)

          // Can have a double check digit calculation!
          total = _doubleCheckCalculation12(vat, total, rules)

          // Establish check digit.
          total = checkDigit(total)

          // Compare it with the last character of the VAT number. If it's the same, then it's valid.
          var expect = +vat.slice(11, 12)
          return total === expect
        }

        return false
      }

      return _check9DigitVat(vat, this.rules) || _check12DigitVat(vat, this.rules)
    },
    rules: {
      multipliers: {
        short: [3, 4, 5, 6, 7, 8, 9, 1],
        med: [1, 2, 3, 4, 5, 6, 7, 8, 9, 1, 2],
        alt: [3, 4, 5, 6, 7, 8, 9, 1, 2, 3, 4]
      },
      check: /^\d{10}1/,
      regex: [/^(LT)(\d{9}|\d{12})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.luxembourg = {
    name: 'Luxembourg',
    codes: ['LU', 'LUX', '442'],
    calcFn: function(vat) {
      var expect = +vat.slice(6, 8)
      var checkDigit = +vat.slice(0, 6) % 89
      // Checks the check digits of a Luxembourg VAT number.

      return checkDigit === expect
    },
    rules: {
      regex: [/^(LU)(\d{8})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.malta = {
    name: 'Malta',
    codes: ['MT', 'MLT', '470'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 6; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits by getting modulus 37.
      total = 37 - total % 37

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(6, 8)
      return total === expect
    },
    rules: {
      multipliers: [3, 4, 6, 7, 8, 9],
      regex: [/^(MT)([1-9]\d{7})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.netherlands = {
    name: 'Netherlands',
    codes: ['NL', 'NLD', '528'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits by getting modulus 11.
      total = total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(8, 9)
      return total === expect
    },
    rules: {
      multipliers: [9, 8, 7, 6, 5, 4, 3, 2],
      regex: [/^(NL)(\d{9})B\d{2}$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.norway = {
    name: 'Norway',
    codes: ['NO', 'NOR', '578'],
    calcFn: function(vat) {
      var total = 0
      var expect
      // See http://www.brreg.no/english/coordination/number.html

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits by getting modulus 11. Check digits > 9 are invalid
      total = 11 - total % 11

      if (total === 11) {
        total = 0
      }

      if (total < 10) {
        // Compare it with the last character of the VAT number. If it's the same, then it's valid.
        expect = +vat.slice(8, 9)
        return total === expect
      }
    },
    rules: {
      multipliers: [3, 2, 7, 6, 5, 4, 3, 2],
      regex: [/^(NO)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.poland = {
    name: 'Poland',
    codes: ['PL', 'POL', '616'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 9; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits subtracting modulus 11 from 11.
      total = total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(9, 10)
      return total === expect
    },
    rules: {
      multipliers: [6, 5, 7, 2, 3, 4, 5, 6, 7],
      regex: [/^(PL)(\d{10})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.portugal = {
    name: 'Portugal',
    codes: ['PT', 'PRT', '620'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits subtracting modulus 11 from 11.
      total = 11 - total % 11
      if (total > 9) {
        total = 0
      }

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(8, 9)
      return total === expect
    },
    rules: {
      multipliers: [9, 8, 7, 6, 5, 4, 3, 2],
      regex: [/^(PT)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.romania = {
    name: 'Romania',
    codes: ['RO', 'ROU', '642'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      var vatLength = vat.length
      var multipliers = this.rules.multipliers.slice(10 - vatLength)

      for (var i = 0; i < vat.length - 1; i++) {
        total += +vat.charAt(i) * multipliers[i]
      }

      // Establish check digits by getting modulus 11.
      total = (10 * total) % 11
      if (total === 10) total = 0

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(vat.length - 1, vat.length)
      return total === expect
    },
    rules: {
      multipliers: [7, 5, 3, 2, 1, 7, 5, 3, 2],
      regex: [/^(RO)([1-9]\d{1,9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.russia = {
    name: 'Russian Federation',
    codes: ['RU', 'RUS', '643'],
    calcFn: function(vat) {
      function _check10DigitINN(vat, rules) {
        var total = 0

        if (vat.length === 10) {
          for (var i = 0; i < 10; i++) {
            total += +vat.charAt(i) * rules.multipliers.m_1[i]
          }

          total = total % 11
          if (total > 9) {
            total = total % 10
          }

          // Compare it with the last character of the VAT number. If it is the same, then it's valid
          var expect = +vat.slice(9, 10)
          return total === expect
        }

        return false
      }

      function _check12DigitINN(vat, rules) {
        var total1 = 0
        var total2 = 0

        if (vat.length === 12) {
          for (var j = 0; j < 11; j++) {
            total1 += +vat.charAt(j) * rules.multipliers.m_2[j]
          }

          total1 = total1 % 11

          if (total1 > 9) {
            total1 = total1 % 10
          }

          for (var k = 0; k < 11; k++) {
            total2 += +vat.charAt(k) * rules.multipliers.m_3[k]
          }

          total2 = total2 % 11
          if (total2 > 9) {
            total2 = total2 % 10
          }

          // Compare the first check with the 11th character and the second check with the 12th and last
          // character of the VAT number. If they're both the same, then it's valid
          var expect = (total1 === +vat.slice(10, 11))
          var expect2 = (total2 === +vat.slice(11, 12))
          return (expect) && (expect2)
        }

        return false
      }

      // See http://russianpartner.biz/test_inn.html for algorithm
      return _check10DigitINN(vat, this.rules) || _check12DigitINN(vat, this.rules)
    },
    rules: {
      multipliers: {
        m_1: [2, 4, 10, 3, 5, 9, 4, 6, 8, 0],
        m_2: [7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0],
        m_3: [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0]
      },
      regex: [/^(RU)(\d{10}|\d{12})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.serbia = {
    name: 'Serbia',
    codes: ['RS', 'SRB', '688'],
    calcFn: function(vat) {
      // Checks the check digits of a Serbian VAT number using ISO 7064, MOD 11-10 for check digit.

      var product = 10
      var sum = 0
      var checkDigit

      for (var i = 0; i < 8; i++) {
        // Extract the next digit and implement the algorithm
        sum = (+vat.charAt(i) + product) % 10
        if (sum === 0) {
          sum = 10
        }
        product = (2 * sum) % 11
      }

      // Now check that we have the right check digit
      var expect = 1
      checkDigit = (product + (+vat.slice(8, 9))) % 10
      return checkDigit === expect
    },
    rules: {
      regex: [/^(RS)(\d{9})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.slovakia_republic = {
    name: 'Slovakia_',
    codes: ['SK', 'SVK', '703'],
    calcFn: function(vat) {
      var expect = 0
      var checkDigit = (vat % 11)
      return checkDigit === expect
    },
    rules: {
      regex: [/^(SK)([1-9]\d[2346-9]\d{7})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.slovenia = {
    name: 'Slovenia',
    codes: ['SI', 'SVN', '705'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digits using modulus 11
      total = 11 - total % 11
      if (total === 10) {
        total = 0
      }

      // Compare the number with the last character of the VAT number. If it is the
      // same, then it's a valid check digit.
      expect = +vat.slice(7, 8)
      return !!(total !== 11 && total === expect)
    },
    rules: {
      multipliers: [8, 7, 6, 5, 4, 3, 2],
      regex: [/^(SI)([1-9]\d{7})$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.spain = {
    name: 'Spain',
    codes: ['ES', 'ESP', '724'],
    calcFn: function(vat) {
      var i = 0
      var total = 0
      var temp
      var expect

      // National juridical entities
      if (this.rules.additional[0].test(vat)) {
        // Extract the next digit and multiply by the counter.
        for (i = 0; i < 7; i++) {
          temp = vat.charAt(i + 1) * this.rules.multipliers[i]
          if (temp > 9)
            total += Math.floor(temp / 10) + temp % 10
          else
            total += temp
        }
        // Now calculate the check digit itself.
        total = 10 - total % 10
        if (total === 10) {
          total = 0
        }

        // Compare it with the last character of the VAT number. If it's the same, then it's valid.
        expect = +vat.slice(8, 9)
        return total === expect
      } else if (this.rules.additional[1].test(vat)) { // Juridical entities other than national ones
        // Extract the next digit and multiply by the counter.
        for (i = 0; i < 7; i++) {
          temp = vat.charAt(i + 1) * this.rules.multipliers[i]
          if (temp > 9)
            total += Math.floor(temp / 10) + temp % 10
          else
            total += temp
        }

        // Now calculate the check digit itself.
        total = 10 - total % 10
        total = String.fromCharCode(total + 64)

        // Compare it with the last character of the VAT number. If it's the same, then it's valid.
        expect = vat.slice(8, 9)
        return total === expect
      } else if (this.rules.additional[2].test(vat)) { // Personal number (NIF) (starting with numeric of Y or Z)
        var tempnumber = vat
        if (tempnumber.substring(0, 1) === 'Y') tempnumber = tempnumber.replace(/Y/, '1')
        if (tempnumber.substring(0, 1) === 'Z') tempnumber = tempnumber.replace(/Z/, '2')
        expect = 'TRWAGMYFPDXBNJZSQVHLCKE'.charAt(+tempnumber.substring(0, 8) % 23)
        return tempnumber.charAt(8) === expect
      } else if (this.rules.additional[3].test(vat)) { // Personal number (NIF) (starting with K, L, M, or X)
        expect = 'TRWAGMYFPDXBNJZSQVHLCKE'.charAt(+vat.substring(1, 8) % 23)
        return vat.charAt(8) === expect
      } else return false
    },
    rules: {
      multipliers: [2, 1, 2, 1, 2, 1, 2],
      regex: [
        /^(ES)([A-Z]\d{8})$/,
        /^(ES)([A-HN-SW]\d{7}[A-J])$/,
        /^(ES)([0-9YZ]\d{7}[A-Z])$/,
        /^(ES)([KLMX]\d{7}[A-Z])$/
      ],
      additional: [
        /^[A-H|J|U|V]\d{8}$/,
        /^[A-H|N-S|W]\d{7}[A-J]$/,
        /^[0-9|Y|Z]\d{7}[A-Z]$/,
        /^[K|L|M|X]\d{7}[A-Z]$/
      ]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.sweden = {
    name: 'Sweden',
    codes: ['SE', 'SWE', '752'],
    calcFn: function(vat) {
      var expect

      // Calculate R where R = R1 + R3 + R5 + R7 + R9, and Ri = INT(Ci/5) + (Ci*2) modulo 10
      var R = 0
      var digit
      for (var i = 0; i < 9; i = i + 2) {
        digit = +vat.charAt(i)
        R += Math.floor(digit / 5) + ((digit * 2) % 10)
      }

      // Calculate S where S = C2 + C4 + C6 + C8
      var S = 0
      for (var j = 1; j < 9; j = j + 2) {
        S += +vat.charAt(j)
      }

      var checkDigit = (10 - (R + S) % 10) % 10

      // Compare it with the last character of the VAT number. If it's the same, then it's valid.
      expect = +vat.slice(9, 10)

      return checkDigit === expect
    },
    rules: {
      regex: [/^(SE)(\d{10}01)$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.switzerland = {
    name: 'Switzerland',
    codes: ['CH', 'CHE', '756'],
    calcFn: function(vat) {
      var total = 0
      for (var i = 0; i < 8; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Establish check digit.
      total = 11 - total % 11
      if (total === 10) return false
      if (total === 11) total = 0

      // Check to see if the check digit given is correct, If not, we have an error with the VAT number
      var expect = +vat.substr(8, 1)
      return total === expect
    },
    rules: {
      multipliers: [5, 4, 3, 2, 7, 6, 5, 4],
      regex: [/^(CHE)(\d{9})(MWST)?$/]
    }
  }

  // eslint-disable-next-line no-undef
  exports.countries.united_kingdom = {
    name: 'United Kingdom',
    codes: ['GB', 'GBR', '826'],
    calcFn: function(vat) {
      var total = 0
      var expect

      // Government departments
      if (vat.substr(0, 2) === 'GD') {
        expect = 500
        return vat.substr(2, 3) < expect
      }

      // Health authorities
      if (vat.substr(0, 2) === 'HA') {
        expect = 499
        return vat.substr(2, 3) > expect
      }

      // Standard and commercial numbers

      // 0 VAT numbers disallowed!
      if (+vat.slice(0) === 0) return false

      // Check range is OK for modulus 97 calculation
      var no = +vat.slice(0, 7)

      // Extract the next digit and multiply by the counter.
      for (var i = 0; i < 7; i++) {
        total += +vat.charAt(i) * this.rules.multipliers[i]
      }

      // Old numbers use a simple 97 modulus, but new numbers use an adaptation of that (less 55). Our
      // VAT number could use either system, so we check it against both.

      // Establish check digits by subtracting 97 from total until negative.
      var checkDigit = total
      while (checkDigit > 0) {
        checkDigit = checkDigit - 97
      }

      // Get the absolute value and compare it with the last two characters of the VAT number. If the
      // same, then it is a valid traditional check digit. However, even then the number must fit within
      // certain specified ranges.
      checkDigit = Math.abs(checkDigit)
      if (checkDigit === +vat.slice(7, 9) && no < 9990001 && (no < 100000 || no > 999999) && (no < 9490001 || no > 9700000)) return true

      // Now try the new method by subtracting 55 from the check digit if we can - else add 42
      if (checkDigit >= 55)
        checkDigit = checkDigit - 55
      else
        checkDigit = checkDigit + 42
      expect = +vat.slice(7, 9)
      return !!(checkDigit === expect && no > 1000000)
    },
    rules: {
      multipliers: [8, 7, 6, 5, 4, 3, 2],
      regex: [
        /^(GB)?(\d{9})$/,
        /^(GB)?(\d{12})$/,
        /^(GB)?(GD\d{3})$/,
        /^(GB)?(HA\d{3})$/
      ]
    }
  }


  //Support of node.js

  if (typeof module === 'object' && module.exports) module.exports = exports

  return exports

})();