/* 
 * Hàm xử lý data gửi từ phía server về
 */
function handlerDataReceiv(data) {

    var place = data;
    var x;
    var address = "";
    for (x in place) {
        for (var y in place[x]) {
            address = place[x][y].houseno + ', ' + place[x][y].street + ', ' + place[x][y].district + ', ' + place[x][y].province + ', ' + place[x][y].national;
            var intro = (place[x][y].intro).trim();
            if(intro == '...'){
                intro = "Thông tin đang được cập nhật từ phía người sở hữu quán";
            }else{
                intro = intro.substring(0,300);
            }
            var row = '<div class="row">' +
                    '<div class="col-xs-6 col-md-3">' +
                    '<a href="/CafeGarden/places/place/' + place[x][y].id + '" class="thumbnail">' +
                    '<img src="/CafeGarden/img/front/' + place[x][y].image + '"/>' +
                    '</a>' +
                    '</div>' +
                    '<div class="details col-md-7">' +
                    '<a href="/CafeGarden/places/place/' + place[x][y].id + '"><h3>' + place[x][y].name + '</h3></a>' +
                    '<p class="address">' + address + '</p>' +
                    '<p class="decription">' +
                    intro +
                    '...</p>' +
                    '</div>' +
                    '<div class="rating col-md-2">' +
                    '<span class="point">' + place[x][y].vote + '.0</span>' +
                    '<ul>' +
                    '<li><span>' + place[x][y].numlike + '</span> lượt thích</li>' +
                    '<li><span>' + place[x][y].view + '</span> lượt xem</li>' +
                    '</ul>' +
                    '</div>' +
                    '</div>';

            $(".show_more_place").append(row);
        }
    }
}

$(function() {
    $status = true;
    $start = 10;
    
    var win = $(window);
    var loading = $("#loading");
    win.on('scroll', function() {
        if ((($(this).scrollTop() / ($(document).height() - $(this).height())) > 0.7) && $status) {
            
            //Kiểm tra có phải lần đầu tiên scroll không
            if($start == 0){
                $start = 10;
            }
            //Khi thanh cuộn hoạt động tì gửi thông tin lên server
            $.ajax({
                type: 'POST',
                url: "advance_search",
                dataType: 'json',
                data: {
                    start: $start,
                    ser: ser,
                    pur: pur,
                    street: street,
                    pro: pro,
                    cat: cat,
                    dist: dist,
                    a : a,
                    orderby: orderby,
                    limit:limit
                },
                beforeSend: function(xhr) {
                    $status = false;
                    loading.show();
                },
                success: function(data, textStatus, jqXHR) {
                    if (data) {
                        handlerDataReceiv(data);
                    } else {
                        win.off('scroll');
                    }
                },
                complete: function() {
                    $status = true;
                    loading.hide();
                    $start += 10;
                }
            });
        }
    });
});

