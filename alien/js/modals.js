(function($) {

    var Modal = function(element, options) {

        var elem = $(element);
        var obj = this;
        var settings = $.extend({
            header: '',
            content: ''
        }, options || {});
        this.publicMethod = function() {
            console.log('public method called!');
        };
        this.destroy = function() {
            elem.removeClass('visible');
            $(".modal-overlay").removeClass('visible');
            setTimeout(function() {
                elem.replaceWith(null)
            }, 200);
        }

        var privateMethod = function() {
            console.log('private method called!');
        };
    };
    $.fn.modal = function(options) {
        return this.each(function() {
            var element = $(this);
            // Return early if this element already has a plugin instance
            if (element.data('modal')) {
                return;
            }

            // pass options to plugin constructor
            var modal = new Modal(this, options);
            element.draggable({
                cursor: "move",
                handle: '.modal-header',
                containment: 'body'
            });
            element.addClass('visible');
            $(element).find('.modal-header').html(options.header);
            $(element).find('.modal-content').html(options.content);
            $(".modal-overlay").addClass('visible');
            // Store plugin object in this element's data
            element.data('modal', modal);
        });
    };

    $(".modal-overlay").live('click', function(){
       modal.destroy();
    });

})(jQuery);
function createModal(json) {

    elem = $('<div class="modal modal-window"><header class="modal modal-header"></header><section class="modal modal-content"></section></div>');
    $('body').append(elem);
    opts = {
        header: json.header,
        content: json.content
    };
    modal = $(elem).modal(opts).data('modal');
    return modal;
}