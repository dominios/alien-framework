
function showDisplayLayoutType(type){
    
    if(!type) return;
    
    $.ajax({
        async: true,
        url: "/alien/ajax.php",
        type: "GET",
        data: "action=displayLayoutType&type="+type,
        timeout: 5000,
        success: function(data){
            $("#viewContent").html(data);
        }
    });
}

function evalConsoleInput(input){
    if(!input) return;    
    $.ajax({
        async: true,
        url: "/alien/ajax.php",
        type: "GET",
        data: "action=evalConsoleInput&data="+input,
        timeout: 5000,
        success: function(data){
            oldData = $("#ConsoleContent").html();
            $("#ConsoleContent").html(oldData+data);
        }
    });
}

$(document).ready(function(){
 
    $(function(){
        $('.rightpanel').tooltip({
            track: false
        });
    });    
  
});

$(document).keyup(function(e) {
    if(e.keyCode == 13 && $("input.ConsoleInput:focus")){
        evalConsoleInput($("input.ConsoleInput").val());
        $("input.ConsoleInput").val('');
    }
});