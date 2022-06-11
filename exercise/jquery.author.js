;(function($){
    $.fn.author = function(name){
        var div = $('<div></div>');
        div.html(name);
        div.css({
            'width': '100%',
            'height': '25px',
            'margin-bottom': '0',
            'background-color': 'lightgrey',
            'text-align': 'center'
          });
        this.append(div);
        return this;
    }
})(jQuery);