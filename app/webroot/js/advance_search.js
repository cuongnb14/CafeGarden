
/**
 * Hàm hiển tị các gợi ý về các quận tìm kiếm
 * @param {type} data
 * @returns {undefined}
 */
function handlerAutocompleteDist(data) {
    var list_dist = new Array();
    var k = 0;
    for (var x in data) {
        for (var y in data[x]) {
            list_dist[k] = data[x][y].district;
            k++;
        }
    }
}
/**
 * 
 * Hàm xử lý dữ liệu các quận gửi về
 * 
 */
function handlerDataProvince(data) {
    //Làm rỗng thẻ chứa các quận
    $("#dis-list").empty();
    var place = data;
    for (var x in place) {
        for (var y in place[x]) {
            var row = '<li class="list-group-item">' +
                    '<span class="badge">14</span>' +
                    '<input value="' + place[x][y].district + '" class="css-checkbox" type="checkbox" />' +
                    '<label class="css-label">' + place[x][y].district + '</label>' +
                    '</li>';
            console.log(row);
            $("#dis-list").append(row);
        }
    }
}

/**
 * 
 * Hàm xử lý đối tượng json gửi từ server về và chèn vào nội dung của trang web
 */

function handlerDataSearch(data) {

    var place = data;
    var x;
    var address = "";
    for (x in place) {
        for (var y in place[x]) {
            address = place[x][y].houseno + ', ' + place[x][y].street + ', ' + place[x][y].district + ', ' + place[x][y].province + ', ' + place[x][y].national;
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
                    (place[x][y].intro).trim().substring(0, 300) +
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

/**
 * 
 * Hàm gửi thông tin các biến yêu cầu tìm kiếm lên 
 */

function submmit_search(p_ser, p_pur, p_street, p_pro, p_cat, p_dist, p_a, p_orderby, p_limit) {

    var $loading = $("#loading");

    //Đặt lại biến toàn cục $start
    $start = 0;

    $.ajax({
        type: 'POST',
        url: "advance_search",
        dataType: 'json',
        data: {
            start: $start,
            ser: p_ser,
            pur: p_pur,
            street: p_street,
            pro: p_pro,
            cat: p_cat,
            dist: p_dist,
            a: p_a,
            orderby: p_orderby,
            limit: p_limit
        },
        beforeSend: function(xhr) {
            $loading.show();
        },
        success: function(data, textStatus, jqXHR) {

            //Kiểm tra đang tìm kiếm ở dạng nào
            if (data && !flag_map) {// Ở dạng danh sách

                //Style lại chiều cao của bản đồ
                $("#google_canvas").css({height: "auto"});
                handlerDataSearch(data);
            } else if (data && flag_map) {// Ở dạng map
                handlerDataReceivMap(data);
            }
        },
        complete: function() {
            $loading.hide();
        }
    });
}

/* 
 * Xử lý các tùy chọn người dùng chọn các tiêu chí tìm kiếm
 */

$(function() {
    flag_map = false; //Biến đánh dấu trạng thái tìm kiếm là bản đồ
    ser = ""; // Lưu các id của dịch vụ được người dùng chọn
    pur = ""; //Lưu các id của các mục đích của quán được chọn
    street = " "; //Lưu tên đường mà người dùng nhập vào
    pro = ""; //Lưu một id của vùng miền mà người dùng chọn
    cat = ""; //Lưu các id của loại món 
    dist = " "; //Lưu tên của quận cần tìm kiếm
    a = " "; //Lưu tên quán cần tìm kiếm
    orderby = "id asc"; //Lưu tùy chọn sắp xếp quán mặc định là tăng dần về id
    limit = 10; //Lưu số quán được hiển thị ra ngoài

    //Bắt sự kiện khi người dùng nhả nút bàn phím để lấy thông tin gợi ý
    $("#dis_text").keyup(function() {
        var content = $(this).val();
        $.ajax({
            type: 'POST',
            url: "search_district",
            dataType: 'json',
            data: {
                content: content
            },
            success: function(data, textStatus, jqXHR) {
                if (data) {
                    handlerAutocompleteDist(data);
                }
            }
        });
    });

    //Tìm tất cả các thẻ input có kiểu là checkbox và bắt sự kiện chuyển đổi giá trị của nó
    $(".mouse_enter").click(function() {
        //Các thẻ checkbox được lựa chọn
        $("input[type='checkbox']").change(function() {

            //Đặt lại biến trạng thái $status nếu cờ flag_map la false
            if (flag_map == true) {
                $status = false;
            }
            else {
                $status = true;
            }

            ser = "";
            pur = "";
            //Lọc ra các thẻ được chọn trong khung dịch vụ
            $("input[value='ser']:checked").each(function() {
                ser = ser + $(this).attr("id") + ",";
            });

            //Lọc ra các thẻ được chọn trong khung mục đích
            $("input[value='pur']:checked").each(function() {
                pur = pur + $(this).attr("id") + ",";
            });

            //Xóa nội dung các quán trước đó
            $(".show_more_place").empty();
        }).trigger('change');

        //Thẻ option select được lựa chọn trong tỉnh thành
        $("#select_province").click(function() {

            $("#select_province").change(function() {
                pro = $("select[id='select_province'] option:selected").attr("value");
                //Gửi thông tin lên server lấy thông tin các quận thuộc tỉnh về
                if (pro !== "") {
                    $.ajax({
                        type: 'POST',
                        url: "search_province",
                        dataType: 'json',
                        data: {
                            pro: pro
                        },
                        success: function(data, textStatus, jqXHR) {
                            if (data) {
                                //Xử lý dữ liệu lấy về
                                handlerDataProvince(data);
                            } else {
                                $(window).off('change');
                            }
                        }
                    });
                }

            }).trigger('change');
        });

        //Gửi thông tin lên server
        submmit_search(ser, pur, street, pro, cat, dist, a, orderby, limit);
    });

    //Bắt sự kiện khi nhấn nút search
    $("#search").click(function() {

        street = $("#search_street").val();
        dist = $("#dis_text").val();
        a = $("#name_text").val();
        //Xóa nội dung các quán trước đó
        $(".show_more_place").empty();
        //Gửi thông tin tìm kiếm lên server
        submmit_search(ser, pur, street, pro, cat, dist, a, orderby, limit);
    });


    //Bắt sự kiện người dùng nhấn vào button xem them danh sách
    $("#search_list").click(function() {
        //Đặt lại giới hạn số quán hiển thị ra
        limit = 10;
        //Đặt lại cờ flag_map
        flag_map = false;

        //Làm rỗng khu vực chèn map
        $("#google_canvas").empty();

        //Ẩn nút xem thêm kết quả của map
        $("#xemthemmap").hide();

        //Bật hiệu ứng scroll
        $status = true;

        //Load lại trang với giá trị tìm kiếm hiện tại
        submmit_search(ser, pur, street, pro, cat, dist, a, orderby, limit);
    });

    //Bắt sự kiện người dùng chọn tùy chọn sắp xếp thay đổi biến orderby

    $("#orderby").change(function() {

        var $loading = $("#loading");

        //Lấy giá trị cần sắp xếp
        orderby = $("select[id='orderby'] option:selected").attr("value");

        //Đặt lại vị trí lấy từ 0
        $start = 0;


        //Làm rỗng nội dung của vung hiển thị danh sách
        $(".show_more_place").empty();

        //Gửi thông tin lên server lấy thông tin các quận thuộc tỉnh về         
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
                a: a,
                orderby: orderby,
                limit: limit
            },
            beforeSend: function(xhr) {
                $loading.show();
            },
            success: function(data, textStatus, jqXHR) {

                //Kiểm tra đang tìm kiếm ở dạng nào
                if (data && !flag_map) {// Ở dạng danh sách
                    //Style lại chiều cao của bản đồ
                    $("#google_canvas").css({height: "auto"});
                    handlerDataSearch(data);
                } else if (data && flag_map) {// Ở dạng map
                    handlerDataReceivMap(data);
                }
            },
            complete: function() {
                $loading.hide();
            }
        });

    }).trigger('change');
//    });
});


