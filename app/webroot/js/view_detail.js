/* 
 * Hiển thị danh sách các loại tìm kiếm
 */

$(function() {
    ser_flags = true;
    $(".panel-body").mouseover(function() {
        $(".list-group").find("input[value='ser_unchecked']").click(function(){
            if(ser_flags){
                ser_flags = false;
                var note = $("<button></button>").text("Dịch vụ");
                $(".list_search").append(note);
            }
        });
    });
});
