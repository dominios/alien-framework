$(function() {

    // init
    $("section.tabs section article").each(function(index) {
        $(this).addClass('notransition');
        $(this).attr('data-height', $(this).height());
        $(this).css('height', $(this).height());
        if (!$(this).hasClass('tab-hidden')) {
            tabs = $('section.tabs section').has('article#' + $(this).attr('id'));
            tabs.addClass('notransition');
            tabs.css('height', $(this).attr('data-height'));
            tabs.height(); // vynuti okamzite preratanie, musi byt, inak sa animuje a to teraz nechceme
            tabs.removeClass('notransition');
        } else {
            $(this).height(0);
        }
        $(this).height(); // vynuti okamzite preratanie, musi byt, inak sa animuje a to teraz nechceme
        $(this).removeClass('notransition');
    });

    // click handler
    $("section.tabs ul li a").live('click', function(ev) {
        section = $(this).parent().parent().parent().parent();
        href = $(this).attr('href');
        $(section).find('li').has('a[href=' + href + ']').toggleClass('active');
        $(section).find('li').has('a[href!=' + href + ']').removeClass('active');
        $(section).find('article:not(.tab-hidden)').addClass('tab-hidden').css('height', '0px');
        activeHref = $(section).find('li.active').find('a').attr('href');
        activeArticle = $(section).find('article' + activeHref)
        newHeight = $(activeArticle).attr('data-height');
        activeArticle.css('height', newHeight).removeClass('tab-hidden');
        $(section).find('section').css('height', !newHeight ? 0 + 'px' : newHeight);
        ev.stopPropagation();
        ev.preventDefault();
    });
});