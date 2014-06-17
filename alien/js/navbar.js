$(function () {

    $('.navbar-content').each(function () {
        if (!$(this).hasClass('navbar-collapsed')) {
            $(this).attr('data-height', $(this).height());
        } else {
            $(this).attr('data-height', 480);
        }
    });

    $(".navbar-toggle").click(function () {
        var $target = $(".navbar-content." + $(this).attr('data-target'));
        $target.height = $target.attr('data-height');
        if (!$target.hasClass('navbar-collapsed')) {
            $($target).height(0);
        } else {
            $($target).height(parseInt($target.attr('data-height')));
        }
        $($target).toggleClass("navbar-collapsed");
    });

});