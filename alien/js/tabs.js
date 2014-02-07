$(function() {

    // init

    // ma mieru alien
    active = getCookie('activeTab');
    if (active && $("section.tabs section article#" + active).length) {
        $("section.tabs section article").removeClass('active').addClass('tab-hidden');
        $("section.tabs section article#" + active).addClass('active').removeClass('tab-hidden');
        $("section.tabs li").has('a[href!=#' + active + ']').removeClass('active');
        $("section.tabs li").has('a[href=#' + active + ']').addClass('active');
    }

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
//            $(this).height(0);
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
        $(section).find('article:not(.tab-hidden)').addClass('tab-hidden').removeClass('tab-active');
        activeHref = $(section).find('li.active').find('a').attr('href');
        activeArticle = $(section).find('article' + activeHref);
        newHeight = $(activeArticle).attr('data-height');
        activeArticle.removeClass('tab-hidden').addClass('tab-active');
        $(section).find('section').css('height', !newHeight ? 0 + 'px' : newHeight);
        ev.preventDefault();
        // na mieru pre alien
        setCookie('activeTab', $(section).find('article' + activeHref).attr('id'));
    });
});