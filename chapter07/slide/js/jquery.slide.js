(function($) {
  function Slide(obj, prefix) {
    // 根据图片个数，自动生成相应数量的小圆点切换按钮
    this.pics = obj.find('li');
    var nav = obj.find('.' + prefix + '-nav');
    nav.append(new Array(this.pics.length + 1).join('<a></a>'));
    this.dots = nav.find('a');
    this.currCls = prefix + '-curr';
    var slide = this;
    // 当鼠标滑到某个小圆点上时，切换到对应的图片
    this.dots.mouseover(function() {
      slide.change($(this).index());
    });
  }
  Slide.prototype = {
    // 切换到索引值为i的图片，speed为动画速度
    change: function(i, speed) {
      this.i = i;    // 保存传入的索引值
      this.dots.eq(i).addClass(this.currCls).siblings('a')
      .removeClass(this.currCls);    // 小圆点切换
      this.pics.eq(i).stop(true, true).fadeIn(speed).siblings('li')
      .fadeOut(speed);               // 图片切换
      return this;
    },
    // 切换到下一张图片，如果已经是最后一张，则自动回到第一张
    next: function() {
      if (++this.i >= this.pics.length) {
        this.i = 0;
      }
      return this.change(this.i, 600);
    },
    // 开始自动切换
    start: function(speed) {
      var slide = this;
      this.timer = window.setInterval(function() {
        slide.next();
      }, speed);
      return this;
    },
    // 暂停自动切换
    pause: function() {
      window.clearInterval(this.timer);
      return this;
    }
  };
  var defaults = {
    speed: 3000,        // 默认切换间隔时间（毫秒）
    width: '670px',     // 默认图片宽度
    height: '240px',    // 默认图片高度
    prefix: 'slide'     // class前缀
  };
  // 为jQuery对象新增slide()方法
  $.fn.slide = function(options) {
    options = $.extend({}, defaults, options);
    this.css({width: options.width, height: options.height});
    var slide = new Slide(this, options.prefix);
    // 鼠标滑到焦点图区域，暂停自动切换，离开时，恢复自动切换
    this.hover(function() {
      slide.pause();
    }, function() {
      slide.start(options.speed);
    });
    return slide.change(0, 0).start(options.speed);
  };
  $.fn.slide.defaults = defaults;
})(jQuery);