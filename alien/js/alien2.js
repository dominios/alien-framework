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

//function markBadInputs() {
//    var session;
//    $.ajaxSetup({cache: false});
//    $.get('formErrorOutput.php', {request: 'read'}, function(data) {
//        session = data;
//    });
//    json = jQuery.parseJSON('');
//    if (json == null)
//        return;
//    for (i = 0; i <= json.length; i++) {
//        item = json.pop();
//        $("input[name=" + item.inputName + "]").addClass('invalidInput');
//        $("<div class=\"inputErrorHelper\">" + item.errorMsg + "</div>").insertAfter($("input[name=" + item.inputName + "]"));
//    }
//}

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
        elem = $('<div class="invalidHelpser" style="margin-top: -' + height + 'px;">' + msg + '</div>');
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
    $("section.tabs ul li a").live('click', function() {
        section = $(this).parent().parent().parent().parent();
        sectionId = section.attr('id');
        href = $(this).attr('href');
        $("#" + sectionId + " article").hide();
        $("#" + sectionId + " article" + href).show();
        $("#" + sectionId + " ul li").removeClass('active');
        $(this).parent('li').addClass('active');
    });



});

$(document).keyup(function(e) {
    if (e.keyCode == 13 && $("input.ConsoleInput:focus")) {
        evalConsoleInput($("input.ConsoleInput").val());
        $("input.ConsoleInput").val('');
    }
});