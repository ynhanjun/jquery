(function($) {
  function pageNav(start, end) {
    var html = '';
    for (var i = start; i <= end; ++i) {
      html += '<span class="' + pre + '-btn">' + i + '</span>';
    }
    $('.' + pre + '-nav').html(html);
    $('.' + pre + '-nav span:contains(' + page + ')').removeClass(pre + '-btn').addClass(pre + '-curr');
    $('.' + pre + '-first').removeClass(pre + '-none').addClass(pre + '-btn');
    $('.' + pre + '-prev').removeClass(pre + '-none').addClass(pre + '-btn');
    $('.' + pre + '-next').removeClass(pre + '-none').addClass(pre + '-btn');
    $('.' + pre + '-last').removeClass(pre + '-none').addClass(pre + '-btn');
    if (page <= 1) {
      $('.' + pre + '-first').removeClass(pre + '-btn').addClass(pre + '-none');
      $('.' + pre + '-prev').removeClass(pre + '-btn').addClass(pre + '-none');
    }
    if (page >= maxPage) {
      $('.' + pre + '-next').removeClass(pre + '-btn').addClass(pre + '-none');
      $('.' + pre + '-last').removeClass(pre + '-btn').addClass(pre + '-none');
    }
  }
  function pageGroup() {
    var mid = Math.floor(num / 2);
    if (num <= mid || page <= mid || num === maxPage) {
      pageNav(1, num);
    } else if (page > maxPage - mid) {
      pageNav(maxPage - num + 1, maxPage);
    } else {
      pageNav(num % 2 > 0 ? page - mid : page - mid + 1, page + mid);
    }
  }
  var defaults = {
    total: 0,
    size: 0,
    num: 5,
    prefix: 'pagelist'
  };
  var page = 1;     // 当前页码值
  var size = 0;     // 每页显示条数
  var total = 0;    // 总记录数
  var num = 0;      // 每页显示的导航链接数
  var maxPage = 0;  // 最大页数
  var pre = '';     // class前缀
  $.fn.page = function(options) {
    var obj = this;
    options = $.extend({}, defaults, options);
    size = options.size;
    total = options.total;
    pre = options.prefix;
    maxPage = Math.ceil(total / size), num = Math.min(options.num, maxPage);
    pageNav(1, num);
    // 分页按钮触发
    this.on('click', '.' + pre + '-btn', function() {
      if ($(this).hasClass(pre + '-first')) {
        page = 1;
      } else if ($(this).hasClass(pre + '-prev')) {
        --page;
      } else if ($(this).hasClass(pre + '-next')) {
        ++page;
      } else if ($(this).hasClass(pre + '-last')) {
        page = maxPage;
      } else {
        page = parseInt($(this).text());
      }
      pageGroup();
    });
    return {
      click: function(callback) {
        obj.on('click', '.' + pre + '-btn', function() {
          callback(page);
        });
     }
    };
  };
  $.fn.page.defaults = defaults;
})(jQuery);