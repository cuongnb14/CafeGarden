
$(function(){
    var flag_comment = false;
    $("#add_comment").click(function(){
            if(flag_comment==false){
                flag_comment = true;
                $("#add_comment").html('Ẩn');
            }else{
                flag_comment = false;
                $("#add_comment").html('Viết bình luận');
            }
            $("#form_comment").toggle();
            $("#content_comment").focus();
        }
    );
    
});