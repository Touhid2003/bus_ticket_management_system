$(function(){
    $("#openForm").click(function(){
        $("#formOverlay").fadeIn(300);
    });

    $("#closeForm, #cancelForm").click(function(){
        $("#formOverlay").fadeOut()(300);
    });
     $("#formOverlay").click(function(e){
        if(e.target === this){
            $("#formOverlay").fadeOut()(300);

        }
  
    });
});