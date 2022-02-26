var Common = {
  content: 'index',
  id: 0,
  init: function() {
    this.getParams(location.href);
    $(document).on('click', '.pjax', function() {
      var url = $(this).attr('href');
      Common.getParams('/' + url);
      Common.getContent();
      history.pushState(undefined, undefined, url);
      return false;
    });
  },
  getParams: function(url) {
    var url = url.match(/\/(\w+)(?:-(\d+))?\.html/);
    if (url !== null) {
        this.content = url[1];
        this.id = url[2] === undefined ? 0 : url[2];
    }
  },
  getContent: function() {
    var content = this.content;
    var categoryList = $('#category_list');
    var categoryShow = $('#category_show');
    $.ajax({
      type: 'GET', url: 'content/' + content + '.html', cache: false,
      success: function (data) {
        if (content === 'index') {
          categoryList.show();
          categoryShow.off('mouseenter').off('mouseleave');
        } else {
          categoryList.hide();
          categoryShow.hover(function () {
            categoryList.show();
          }, function() {
            categoryList.hide();
          });
        }
        $('#content').html(data);
      }
    });
    $.get(Config.api + 'categories?view=nav', function (data) {
      var html = template('category_list_tpl', {item: data});
      categoryList.html(html);
      $('.category-item').hover(function () {
        $(this).find('.category-sub').show();
        $(this).children('.category-main').children('a').addClass('on');
      }, function () {
        $(this).find('.category-sub').hide();
        $(this).children('.category-main').children('a').removeClass('on');
      });
    });
  },
  itemPicPath: function(data) {
    for (var i in data) {
      if (data[i].pic === '') {
        data[i].pic = 'img/preview.jpg';
      } else {
        data[i].pic = Config.uploadURL + data[i].pic;
      }
    }
  },
  itemAlbumPath: function(data) {
    for (var i in data) {
      data[i] = Config.uploadURL + data[i].replace('[prefix]', 'album_small_');
    }
  },
  slidePath: function(data) {
    for (var i in data) {
      data[i].pic = Config.uploadURL + data[i].pic;
    }
  },
  addCart: function(id, num) {
    id = parseInt(id);
    num = parseInt(num);
    var data = this.getCart();
    var found = false;
    for (var i in data) {
      if (data[i].id === id) {
        found = i;
        break;
      }
    }
    if (found === false) {
      data.unshift({id: id, num: num});
    } else {
      data[found].num += num;
    }
    localStorage.setItem('shop_front_cart', JSON.stringify(data));
  },
  getCart: function() {
    var data = JSON.parse(localStorage.getItem('shop_front_cart'));
    return data === null ? [] : data;
  },
  editCart: function(id, num) {
    id = parseInt(id);
    num = parseInt(num);
    var data = this.getCart();
    for (var i in data) {
      if (data[i].id === id) {
        data[i].num = num;
        if (num <= 0) {
          data.splice(i, 1);
        }
        break;
      }
    }
    localStorage.setItem('shop_front_cart', JSON.stringify(data));
  }
};
Common.init();
Common.getContent();