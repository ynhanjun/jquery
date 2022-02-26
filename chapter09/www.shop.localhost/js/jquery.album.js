(function($) {
  var defaults = {
    prefix: 'album'
  };
  var pre = '';
  $.fn.album = function(options) {
    options = $.extend({}, defaults, options);
    pre = options.prefix;
    // 底部预览图移动
    (function() {
      var i = 0;
      var count = $('.' + pre + '-list li').length - 4;
      var width = $('.' + pre + '-list li:first').width();
      var left = true;
      var right = false;
      $('.' + pre + '-btn-left').click(function() {
        if (left) {
          return false;
        }
        --i;
        move();
      });
      $('.' + pre + '-btn-right').click(function() {
        if (right) {
          return false;
        }
        ++i;
        move();
      });
      function move() {
        left = i === 0;
        right = i === count - 1;
        $('.' + pre + '-list ul').stop(false, true).animate({marginLeft: -i * width + 'px'}, 600);
      }
    })();
    // 滑过小图查看大图
    (function () {
      var mid = $('.' + pre + '-mid');
      var mask = $('.' + pre + '-mask');
      var big = $('.' + pre + '-big');
      var img = $('.' + pre + '-big-img');
      // 鼠标经过盒子 显示遮罩和大图 
      mid.mouseover(function() {
        mask.show();
        big.show();
      });
      // 鼠标离开盒子 隐藏遮罩和大图
      mid.mouseout(function() {
        //隐藏遮罩和大图
        mask.hide();
        big.hide();
      });
      //  鼠标在盒子上移动的时候
      //  让遮罩跟着鼠标移动
      mid.mousemove(function(event) {
        //鼠标在页面中的坐标
        var pageX = event.pageX;
        var pageY = event.pageY;
        //  计算鼠标的位置 距 盒子 的距离
        var boxX = pageX - mid.offset().left;
        var boxY = pageY - mid.offset().top;
        // 计算遮罩的位置
        var maskX = boxX - mask.outerWidth() / 2;
        var maskY = boxY - mask.outerHeight() / 2;
        // 限定遮罩可移动范围
        if (maskX < 0) {
          maskX = 0;
        }
        if (maskX > mid.outerWidth() - mask.outerWidth()) {
          maskX = mid.outerWidth() - mask.outerWidth();
        }
        if (maskY < 0) {
          maskY = 0;
        }
        if (maskY > mid.outerHeight() - mask.outerHeight()) {
          maskY = mid.outerHeight() - mask.outerHeight();
        }
        //  修改遮罩的显示位置
        mask.css({left: maskX, top: maskY});
        // 按照比例移动大图 大图片能够移动的总距离 = 大图的宽度 - 大盒子的宽度
        var bigImgToMove = img.outerWidth() - big.outerWidth();
        // 遮罩能够移动的总距离 = 小盒子的宽度 - 遮罩的宽度
        var maskToMove = mid.outerWidth() - mask.outerWidth();
        // 计算移动比例rate = 大图片能够移动的总距离/遮罩能够移动的总距离
        var rate = bigImgToMove / maskToMove;
        // 设置大图片当前的位置 = rate * 遮罩当前的位置
        img.css({left: -rate * maskX, top: -rate * maskY});
      });
    })();
    // 单击小图切换大图
    (function() {
      $('.' + pre + '-list li').click(function() {
        var src = $(this).find('img').attr('src');
        $('.' + pre + '-mid-img').attr('src', src.replace('small', 'mid'));
        $('.' + pre + '-big-img').attr('src', src.replace('small', 'big'));
        $(this).addClass(pre + '-curr').siblings().removeClass(pre + '-curr');
      });
      $('.' + pre + '-list li:first').click();
    })();
  };
  $.fn.album.defaults = defaults;
})(jQuery);