/** 
 * Dùng để ẩn hiện các mục trong danh sách các mục đích của quán
 */

$(function(){
    $("#purport_show").click(function(){
        if(!$(this).hasClass("click_purport_show")){
            $(this).addClass("click_purport_show").text("<< Thu hẹp");
            $(".list-group").find("li[value='more_pur']").slideDown("slow"); 
        }else{
            $(this).removeClass("click_purport_show").text("Xem thêm >> ");
            $(".list-group").find("li[value='more_pur']").slideUp("slow"); 
        }
        
    });
    $("#service_show").click(function(){
        if(!$(this).hasClass("click_service_show")){
            $(this).addClass("click_service_show").text("<< Thu hẹp");
            $(".list-group").find("li[value='more_service']").show("slow"); 
        }else{
            $(this).removeClass("click_service_show").text("Xem thêm >> ");
            $(".list-group").find("li[value='more_service']").hide("slow"); 
        }
        
    });
    
    
    
});

