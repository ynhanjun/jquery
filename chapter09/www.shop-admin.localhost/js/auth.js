(function () {
  var token = localStorage.getItem(Config.tokenName);
  Config.token = token;
  if (token === null) {
    alert('您还没有登录！');
    location.href = 'login.html';
    return;
  }
  $.ajaxSetup({headers: {Authorization: token}});
  $.get(Config.api + 'test').fail(function(xhr) {
    var data = JSON.parse(xhr.responseText);
    if (data.error === 'auth') {
      alert(data.msg + '。\n\n请重新登录！\n');
      location.href = 'login.html';
    }
  });
})();