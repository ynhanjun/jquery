var Config = {
  api: 'http://api.shop-admin.localhost/v1/',
  uploadURL: 'http://www.shop.localhost/upload/',
  tokenName: 'shop_admin_auth_token',
  xssFilter: (function() {
    var obj = $('<div></div>');
    return function(data) {
      for (var i in data) {
        if (typeof data[i] === 'object') {
          data[i] = arguments.callee(data[i]);
        } else if (typeof data[i] === 'string') {
          data[i] = obj.text(data[i]).html();
        }
      }
      return data;
    };
  })()
};