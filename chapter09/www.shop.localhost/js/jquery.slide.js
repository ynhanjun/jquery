(function($, window) {
  function Slide(obj, prefix) {
    this.pics = obj.find('li');
    var nav = obj.find('.' + prefix + '-nav');
    nav.append(new Array(this.pics.length + 1).join('<a></a>'));
    this.dots = nav.find('a');
    this.currCls = prefix + '-curr';
    var slide = this;
    this.dots.mouseover(function() {
      slide.change($(this).index());
    });
  }
  Slide.prototype = {
    change: function(i, speed) {
      this.i = i;
      this.dots.eq(i).addClass(this.currCls).siblings('a').removeClass(this.currCls);
      this.pics.eq(i).stop(true, true).fadeIn(speed).siblings('li').fadeOut(speed);
      return this;
    },
    next: function() {
      if (++this.i >= this.pics.length) {
        this.i = 0;
      }
      return this.change(this.i, 600);
    },
    start: function(speed) {
      var slide = this;
      this.timer = window.setInterval(function() {
        slide.next();
      }, speed);
      return this;
    },
    pause: function() {
      window.clearInterval(this.timer);
      return this;
    }
  };
  var defaults = {
    speed: 3000,
    width: '670px',
    height: '240px',
    prefix: 'slide'
  };
  $.fn.slide = function(options) {
    options = $.extend({}, defaults, options);
    this.css({width: options.width, height: options.height});
    var slide = new Slide(this, options.prefix);
    this.hover(function() {
      slide.pause();
    }, function() {
      slide.start(options.speed);
    });
    return slide.change(0, 0).start(options.speed);
  };
  $.fn.slide.defaults = defaults;
})(jQuery, this);