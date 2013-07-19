
function showDisplayLayoutType(type){
    
    if(!type) return;
    
    $.ajax({
        async: true,
        url: "ajax.php",
        type: "GET",
        data: "action=displayLayoutType&type="+type,
        timeout: 5000,
        success: function(data){
            $("#viewContent").html(data);
        }
    });
}


$(document).ready(function(){
 
    $(function(){
        $(document).tooltip({
            track: true
        });
    });
  
});