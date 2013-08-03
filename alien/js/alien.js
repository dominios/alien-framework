/* ######################################### */
/*                                           */
/* ALiEN JavaScript functions and AJAX calls */
/* DO NOT EDIT IN ANY CASE !!                */
/*                                           */
/* ######################################### */

var collapseByDefault = true;

/* ********* STATIC FUNCTIONS ***********************************************************/

var vybranyTypObjektu=true;
var sortableOriginalContent = new Array();

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - this.outerHeight()) / 2) + 
        $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - this.outerWidth()) / 2) + 
        $(window).scrollLeft()) + "px");
    return this;
}

jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ? 
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}

/* **** VSEOBECNE ********** */

function loaderOn(){
    $("#loader").center().show();
}

function loaderOff(){
    $("#loader").center().fadeOut(150);
}

function logout(){
    $("#logout").submit();
}

function refreshCaptcha(){
    var img = document.images['captchaimg'];
    img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}

function toggleHideable(id){
    $('#hideable-'+id).slideToggle(150);
}

// zobrazi manualovu stranku
function viewMan(page){
    if(!page) return;
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=viewMan&page="+page,
        timeout: 10000,
        success: function(data) {
            $('#windowContent').html(data)
            openWindow();
        }
    });
}

// otvori file browser
function openBrowserWindow(folder,filter){    
    if(!folder) return false;
    if(!filter) filter = 'none';
    url ='fileBrowser.php?open=false&root='+folder+"&filter="+filter;
    window.open(url,'_blank','left=0,width=500,height=500');
    return true;
}

// vyznaci aktualnu kolonku v lavom menu
function highlightLeftmenu(seolink){
    selItem = $(".leftpanel > a:regex(href, .*"+seolink+".*)").html();
    selText = '<div style="float: right;"><img src="images/icons/bullet_arrow_left.png" style="height: 21px;"></div>';
    $(".leftpanel > a:regex(href, .*"+seolink+".*)").html(selItem + selText);
}

// zobrazi z tempu notifikacie
function showNotifications(){
    $("#notifyArea").slideDown(500, function(){
       $(this).delay(4000).slideUp();
    });
}

function openWindow(){
    w=$("#window");
    h=window.outerHeight;
    w=w.width();
    x=($("body").width()/2)-(w/2)-80;
    y=($("body").height()/2)-(h/2)-80;
    if(y<0){
        y=20;
    }
    $("#window").css("left",x+"px");
    $("#window").css("top",y+"px");
    $("#window").center();
    $("#window").fadeIn(250);
    $("#overlay").fadeIn(250);
}

function closeWindow(){
    if(!vybranyTypObjektu){
        alert('Choose from the list first.');
        return;
    }
    $("#window").fadeOut(250,function() {$("#windowContent").html('');});
    $("#overlay").fadeOut(250);
}

function toggleFormWindow(button){
    if(!button) return;
    id = $(button).parent().parent().find(".formWindowContent").attr("id")
    img = $(button).find('img');
    if($(img).attr('src')=='images/icons/tab_add.png'){
        $(img).attr('src','images/icons/tab_delete.png');
    } else {
        $(img).attr('src','images/icons/tab_add.png');
    }
    $("#"+id+".formWindowContent").slideToggle();
}

function showAjaxLoader(div){
    $(div).html('<div class="ajaxLoader"><img src="images/ajax-loader.gif"> Loading...</div>');
    $("div.ajaxLoader").fadeIn(2000);
}

function turnSortableOn(){
    $(".button#sortableTurnOn").hide();
    $(".button#sortableCancel").show();
    $(".item").css("cursor","move");
    $(".sortable .item").css("borderColor","#FBF37B");
    if($(".sortable").hasClass('grid')){
        $(".sortable").sortable({
            items: "> div",
            cursor: "move",
            delay: 100,
            opacity: 0.75,
            revert: 200,
            scroll: true,
            scrollSensitivity: 8,
            scrollSpeed: 8
        });
    } else {
        $(".sortable").sortable({
            placeholder: "ui-state-highlight",
            items: "> div",
            cursor: "move",
            delay: 100,
            opacity: 0.75,
            revert: 200,
            scroll: true,
            scrollSensitivity: 8,
            scrollSpeed: 8
        });
    }
    i=0;
    $(".sortable").each(function(){
       sortableOriginalContent[i++] = $(this).html();
    });
    $(".sortable").sortable("enable");
    $(".sortable").disableSelection();
}

function turnSortableOff(save){
    if($(".sortable").sortable("option","disabled")){
        return;
    }
    $(".button#sortableTurnOn").show();
    $(".button#sortableCancel").hide();
    if(save){
        $(".sortable").each(function(){
            order=$(this).sortable("toArray");
            id=$(this).attr('id');
            $('input[name="order-'+id+'"]').attr("value",order.toString());
        });
    } else {
        i=0;
        $(".sortable").each(function(){
            $(this).sortable( "refreshPositions");
            $(this).html(sortableOriginalContent[i++]);
        });
    }
    $(".item").css("cursor", "inherit");
    $(".sortable .item").css("borderColor","");
    $(".sortable").sortable("disable");
}

function saveSorting(action, id){ // action: ktory handler sa ma spustit, id1 napr, id stranky
    if(!action){
        action = "contentSortItems";
    }
    if(!id){
        id = 0;
    }
    turnSortableOff(true);
    loaderOn();
    sendData = '';
    $('input:regex(name, order-sortable-.*)').each(function(){
        name = $(this).attr("name");
        value = $(this).attr("value");
        sendData += name+"="+value+"&";
    });
    openWindow(); 
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do="+action+"&id="+id+"&"+sendData,
        timeout: 5000,
        success: function(data){
            $("#windowContent").html(data);
            loaderOff();
        }
    });
}

// USERS

function usersAddPermissionToGroup(id_g, id_p, value, refresh){
    if(!id_g || !id_p) return;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=usersAddPermissionToGroup&id_g="+id_g+"&id_p="+id_p+"&value="+value,
        timeout: 5000
    }).done(function(){
        if(refresh==true){
            window.location.reload();
        }
    });
}

function usersRemovePermissionOfGroup(id_g, id_p, value, refresh){
    if(!id_g || !id_p) return;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=usersRemovePermissionOfGroup&id_g="+id_g+"&id_p="+id_p+"&value="+value,
        timeout: 5000
    }).done(function(){
        if(refresh==true){
            window.location.reload();
        }
    });
}

function usersAddGroupOfUser(id_g, id_u){
    if(!id_g || !id_u) return;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=usersAddGroupOfUser&id_g="+id_g+"&id_u="+id_u,
        timeout: 5000,
        success: function(data){
            $("#windowCurrentGroups").html(data);
            loaderOff();
        }
    });
}

function usersRemoveGroupOfUser(id_g, id_u, refresh){
    if(!id_g || !id_u) return;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=usersRemoveGroupOfUser&id_g="+id_g+"&id_u="+id_u,
        timeout: 5000,
        success: function(data){
            $("#windowCurrentGroups").html(data);
            loaderOff();
        }
    }).done(function(){
        if(refresh==true){
            window.location.reload();
        }
    });
}

function usersShowGroupInformation(id_g, id_u){    
    if(!id_g) return;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=usersShowGroupInformation&id_g="+id_g+"&id_u="+id_u,
        timeout: 5000,
        success: function(data){
            loaderOff();
            $("#windowGroupInfo").html(data);
        }
    });
}

function usersShowGroupManager(){
    id_u=$("input[name='editid']").val();
    if(!id_u || id_u<1) return;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=showGroupManager&id_u="+id_u,
        timeout: 5000,
        success: function(data){
            $("#windowContent").html(data);
            openWindow();
            loaderOff();
        }
    });
}

function usersRefreshUserList(page){
    if(page==null){ 
        page=1;            
    }
    loaderOn();
    findName=$("#findName").val();
    limit=$("#numberOfReults").val(); 
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
//          data: "do=viewlist&limit=50&orderby=id&ordertype=asc",
        data: "do=viewlist&limit="+limit+"&page="+page+"&searchstring="+findName,
        timeout: 10000,
        success: function(data) {
            loaderOff();
            $('#userContent').html(data);
        }
    });
}

// pre user list
function goToPage(page){
    usersRefreshUserList(page);
}


// FUNCKIE PRE OBSAH

function contentToggleLeftMenu(string){
    if(!string) return;
    if(string=='browser'){
        $('#contentBrowserPanel').slideToggle(300);
        $('#contentPluginsPanel').slideUp(300);
    } else if(string=='plugins'){
        $('#contentBrowserPanel').slideUp(300);
        $('#contentPluginsPanel').slideToggle(300);
    }
}

function contentViewAddGroup(id_v, id_g){
    if(!id_g || !id_v) return;
    refresh=true;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentViewAddGroup&idg="+id_g+"&idv="+id_v,
        timeout: 5000
    }).done(function(){
        if(refresh==true){
            window.location.reload();
        }
    });
}

function contentViewRemoveGroup(id_v, id_g){
    if(!id_g || !id_v) return;
    refresh=true;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentViewRemoveGroup&idg="+id_g+"&idv="+id_v,
        timeout: 5000
    }).done(function(){
        if(refresh==true){
            window.location.reload();
        }
    });
}

function contentViewAddPermission(id_v, id_p){
    if(!id_p || !id_v) return;
    refresh=true;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentViewAddPermission&idp="+id_p+"&idv="+id_v,
        timeout: 5000
    }).done(function(){
        if(refresh==true){
            window.location.reload();
        }
    });
}

function contentViewRemovePermission(id_v, id_p){
    if(!id_p || !id_v) return;
    refresh=true;
    loaderOn();
    $.ajax({
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentViewRemovePermission&idp="+id_p+"&idv="+id_v,
        timeout: 5000
    }).done(function(){
        if(refresh==true){
            window.location.reload();
        }
    });
}

function contentShowViewAddGroup(id_v){
    if(!id_v) return;
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentViewAddGroupList&idv="+id_v,
        timeout: 10000,
        success: function(data) {
            $('#windowContent').html(data)
            openWindow();
        }
    });
}

function contentShowViewAddPermission(id_v){
    if(!id_v) return;
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentViewAddPermissionList&idv="+id_v,
        timeout: 10000,
        success: function(data) {
            $('#windowContent').html(data)
            openWindow();
        }
    });
}

function contentShowChooseFile(type){
    if(!type) return;
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentShowChooseFile&type="+type,
        timeout: 10000,
        success: function(data) {
            $('#windowContent').html(data)
            openWindow();
        }
    });
}

function contentShowChooseTemplate(){    
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentSelectTemplateList",
        timeout: 10000,
        success: function(data) {
            $('#windowContent').html(data)
            openWindow();
        }
    });
}

function contentShowChooseItemType(type){
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentShowChooseItemType&type="+type,
        timeout: 10000,
        success: function(data) {
            $('#windowContent').html(data);
            vybranyTypObjektu=false;
            openWindow();
        }
    });
}

function contentSelectWhatToAdd(type,id,box){
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentSelectWhatToAdd&task="+type+"&parentId="+id+"&parentBox="+box,
        timeout: 10000,
        success: function(data) {
            $('#windowContent').html(data)
            openWindow();
        }
    });
}

function contentGenerateNewItemForm(type){
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentGenerateNewItemForm&type="+type,
        timeout: 10000,
        success: function(data){
            oldData = $('#itemFormContent').html();
            $('#itemFormContent').html(data+oldData);
        }
    });
}

function contentShowChooseViewType(tid, pid, box){
    if(!box && !(tid || pid)){
        return;
    }
    $.ajax({            
        async: true,
        url: "ajax_handler.php",
        type: "GET",
        data: "do=contentShowChooseViewType&tid="+tid+"&pid="+pid+"&box="+box,
        timeout: 10000,
        success: function(data) {
            $('#windowContent').html(data)
            openWindow();
        }
    });
}

/* ********* ACTIVE SCRIPTS *************************************************************/

$(document).keyup(function(e) {
  if(e.keyCode === 27) { closeWindow(); }   // esc
  
  if(e.keyCode === 192) { 
      $("#ConsoleContainer").fadeToggle(75); 
      $(".ConsoleInput").focus();
//      $(".ConsoleInput").val('');
  } 

});


$(document).ready(function($) {
    
    $("#ConsoleContainer").hide();
    
    $("a:regex(href, page)").live('click',function(){
        if($(this).attr('target') ==='_blank') return;
        loaderOn();
    });
    
    $("form").live('submit',function(){
        loaderOn();
    })
    
    $(".button:regex(onClick, location").live('click',function(){
       loaderOn(); 
    });

    $(".toggleHideable").live('click',function(){
        obj = $(this);
//        alert('aaa');
        if(obj.hasClass('more')){
            obj.attr('src','images/icons/less.png');
            obj.removeClass('more').addClass('less');
        } else {
            obj.attr('src','images/icons/more.png');
            obj.removeClass('less').addClass('more');
        }
    });

    
    if(collapseByDefault==1){
        $("div.formWindowContent").css("display","none");
        $("div.formWindowMinimizer > img").attr("src","images/icons/tab_add.png");
    }

    // vypnut vyskakovacku
    $("#windowCloser").live('click',function(){
        closeWindow();
    });
    
    $(".formWindowMinimizer").live('click',function(){
        toggleFormWindow($(this));
    });
    
    //vybrat sablonu zo zoznamu
    $(".item.template").live("click",function(){
        $(this).css("backgroundColor","#FBF37B");
        $("#selTemplate").html($(this).find(".tmpName").html());
        $("#frmSelTemplate").val($(this).attr('id'))
        closeWindow();
    });
    
    $(".item.newObject").live("click",function(){
        $(this).css("backgroundColor","#FBF37B");
        closeWindow();
    });
    
    $(".item.objectType").live("click",function(){
        contentGenerateNewItemForm($(this).attr('id'));
        $(this).css("backgroundColor","#FBF37B");
        vybranyTypObjektu=true;
        closeWindow();
    });
    
    $(".item.fileChooser").live("click",function(){
        if($(this).hasClass('php')){
            $("input[name='templateHtml']").attr('value', $(this).attr('id'));
            closeWindow();
        }
        if($(this).hasClass('css')){
            $("input[name='templateCss']").attr('value', $(this).attr('id'));
            closeWindow();
        }
        if($(this).hasClass('ini')){
            $("input[name='templateConfig']").attr('value', $(this).attr('id'));
            closeWindow();
        }
    });
    
    // datepicker pri vyberani BANanu
    $("#datepicker").datepicker();
    $("#datepicker").datepicker( "option" , "dateFormat" , "yy-mm-dd" );

    $("#numberOfReults").change(function(){usersRefreshUserList();});
    $("#findName").change(function(){usersRefreshUserList();});
    $("#findName").keydown(function(){usersRefreshUserList();});
    $("#findName").keyup(function(){usersRefreshUserList();});
    $("#findRefreshButton").click(function(){usersRefreshUserList();});

    $(".toggleDetails").live('click',function(){
        $('.toggleDetails').not(this).parent().next().slideUp();
        $('.toggleDetails').not(this).find('img').attr('src','images/icons/zoom_in.png');       
        if( $(this).find('img').attr('src')=='images/icons/zoom_in.png'){
            $(this).find('img').attr('src','images/icons/zoom_out.png');
        } else {
            $(this).find('img').attr('src','images/icons/zoom_in.png');
        }
        $(this).parent().next().slideToggle();
        $('html, body').animate({ 
            scrollTop: $(this).offset().top-235 
        },1000); 
       
   });
  
});