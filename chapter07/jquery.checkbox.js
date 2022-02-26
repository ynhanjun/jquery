(function($) {
    function Checkbox(ele) {
        this.ele = ele;
    }
    Checkbox.prototype = {
        checkAll: function() {          // 全选
            this.ele.prop('checked', true);
        },
        uncheckAll: function() {        // 全不选
            this.ele.prop('checked', false);
        },
        checkInvert: function() {       // 反选
            this.ele.each(function() {
                this.checked = !this.checked;
            });
        }
    };
    $.checkbox = function(selector) {
        return new Checkbox($(selector));
    };
})(jQuery);