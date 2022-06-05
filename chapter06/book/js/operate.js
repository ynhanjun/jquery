(function() {
    function Booklist(obj) {
        this.obj = obj;
        var fields = [];
        obj.find('tr:first th[data-name]').each(function() {
            fields.push($(this).attr('data-name'));
        });
        this.fields = fields;
    }
    Booklist.prototype = {
        append: function(data, opt) {
            var arr = [];
            $.each(this.fields, function() {
                arr.push(data[this]);
            });
            var html = '<td>' + arr.join('</td><td>')  + '</td><td>' + opt.join('｜') + '</td>';
            this.obj.append('<tr data-id="' + data.id + '">' + html + '</tr>');
        },
        update: function(i, data) {
            var obj = this.obj.find('tr:eq(' + i + ')').find('td');
            $.each(this.fields, function(i) {
                obj.eq(i).text(data[this]);
            });
        },
        getData: function(i) {
            var data = {};
            var obj = this.obj.find('tr:eq(' + i + ')').find('td');
            $.each(this.fields, function(i) {
                data[this] = obj.eq(i).text();
            });
            return data;
        },
        remove: function(i) {
            this.obj.find('tr:eq(' + i + ')').remove();
        },
        search: function(keyword) {
            var len = this.fields.length;
            this.obj.find('tr:gt(0)').each(function() {
                var show = true;
                $(this).find('td:lt(' + len + ')').each(function() {
                    show = $(this).text().indexOf(keyword) !== -1;
                    if (show) {
                        return false;
                    }
                });
                $(this).toggle(show);
            });
        }
    };
    function Editor(obj) {
        this.obj = obj;
    }
    Editor.prototype = {
        fill: function(data) {
            this.empty();
            for (var k in data) {
                this.obj.find('[name=' + k + ']').val(data[k]);
            }
        },
        getData: function() {
            var data = {};
            this.obj.find('input[name]').each(function() {
               data[$(this).attr('name')] = $(this).val();
            });
            return data;
        },
        empty: function() {
            this.obj.find('input[name]').val('');
        }
    };
    var serverUrl = 'process.php';
    var list = $('.booklist');
    var edit = $('.edit');
    var opt = [
        '<a href="#" class="booklist-edit">修改</a>',
        '<a href="#" class="booklist-del">删除</a>'
    ];
    var booklist = new Booklist(list.find('table'));
    var editor = new Editor(edit);
    $.getJSON(serverUrl, function(data) {
        for (var i in data) {
          booklist.append(data[i], opt);
        }
    });
    list.on('click', '.booklist-del', function() {
        var tr = $(this).parents('tr'), i = tr.index(), id = tr.attr('data-id');
        $.post(serverUrl + '?action=del&id=' + id, function() {
            booklist.remove(i);
        });
        return false;
    });
    list.on('click', '.booklist-edit', function() {
        var i = $(this).parents('tr').index();
        var data = booklist.getData(i);
        var attr = {'data-action': 'update', 'data-i': i, 'data-id': data.id};
        editor.fill(data);
        edit.attr(attr).fadeIn(200);
        return false;
    });
    $('.edit-save').click(function() {
        var action = edit.attr('data-action');
        var data = editor.getData();
        if (action === 'update') {
            var id = edit.attr('data-id');
            $.post(serverUrl + '?action=update&id=' + id, data, function() {
                var i = edit.attr('data-i');
                booklist.update(i, data);
                edit.hide();
            }).fail(function(xhr) {
                alert('保存失败，错误信息：' + xhr.responseText);
            });
        } else if(action === 'add') {
            $.post(serverUrl + '?action=add', data, function() {
                booklist.append(data, opt);
                edit.hide();
            }).fail(function(xhr) {
                alert('保存失败，错误信息：' + xhr.responseText);
            });
        }
    });
    $('.edit-cancel').click(function() {
        edit.hide();
    });
    $('.edit-bg').click(function() {
        edit.hide();
    });
    $('.add').click(function() {
        editor.empty();
        edit.attr('data-action', 'add').fadeIn(200);
    });
    $('.search').click(function() {
        booklist.search($.trim($('.search-input').val()));
    });
})();