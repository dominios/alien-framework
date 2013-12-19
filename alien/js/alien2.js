function createDialog(header, content) {
    $("#dialog-modal").remove();
    newhtml = "<div id='dialog-modal' title='" + header + "'>";
    newhtml += "<div id='dialog-content'><p>" + content + "</p></div>";
    newhtml += "</div>";
    $("body").append(newhtml);
    $(function() {
        $("#dialog-modal").dialog({
            modal: true,
            width: 'auto',
            height: 'auto',
            show: {
                effect: 'drop',
                duration: 100
            },
            hide: {
                effect: 'drop',
                duration: 100
            }
        });
    });
}

function showFilePreview(file) {
    if (!file)
        return;
    $.ajax({
        async: true,
        url: "/alien/ajax.php",
        type: "GET",
        data: "action=showFilePreview&file=" + file,
        timeout: 5000,
        success: function(data) {
            json = jQuery.parseJSON(data);
            createDialog(json.header, json.content);
            if ($("#dialog-modal").width() > 1000) {
                $("#dialog-modal").width(1000);
            }
            if ($("#dialog-modal").height() > 550) {
                $("#dialog-modal").height(550);
            }
        }
    });
}

function showDisplayLayoutType(type) {

    if (!type)
        return;
    $.ajax({
        async: true,
        url: "/alien/ajax.php",
        type: "GET",
        data: "action=displayLayoutType&type=" + type,
        timeout: 5000,
        success: function(data) {
            $("#viewContent").html(data);
        }
    });
}

function evalConsoleInput(input) {
    if (!input)
        return;
    $.ajax({
        async: true,
        url: "/alien/ajax.php",
        type: "GET",
        data: "action=evalConsoleInput&data=" + input,
        timeout: 5000,
        success: function(data) {
            oldData = $("#ConsoleContent").html();
            $("#ConsoleContent").html(oldData + data);
        }
    });
}

$(document).ready(function($) {

    /* vygenerovanie error hlasky pod input */
    $(".invalid[data-errorMsg]").each(function(i) {
        msg = $(this).attr('data-errorMsg');
        width = $(this).width();
        height = $(this).height();
        elem = $('<div class="invalidHelper" style="margin-top: -' + height + 'px;">' + msg + '</div>');
        $(this).after(elem);
        elem.css('margin-left', width - elem.width() + 'px');
    });

    /* terminalove okno */
    $("#ConsoleContainer").hide();

    /* vypnutie jQuery UI tooltipov pre rightpanel */
    $(function() {
        $('.rightpanel').tooltip({
            track: false
        });
    });

    /* pre istotu buttony */
    $('.button.disabled, button.disabled').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    $('.button.disabled').removeAttr('onclick');

    /* taby */
    $("section.tabs ul li a").live('click', function(ev) {
        section = $(this).parent().parent().parent().parent();
        href = $(this).attr('href');
        $(section).find('li').has('a[href=' + href + ']').toggleClass('active');
        $(section).find('li').has('a[href!=' + href + ']').removeClass('active');
        $(section).find('article').hide();
        activeHref = $(section).find('li.active').find('a').attr('href');
        $(section).find('article' + activeHref).show();
        ev.stopPropagation();
        ev.preventDefault();
    });



});

$(document).keyup(function(e) {
    if (e.keyCode === 13 && $("input.ConsoleInput:focus")) {
        evalConsoleInput($("input.ConsoleInput").val());
        $("input.ConsoleInput").val('');
    }
});