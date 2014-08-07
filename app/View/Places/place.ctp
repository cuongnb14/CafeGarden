<?php
$sp = array();
foreach ($ser as $value) {
    $sp[] = $value['ServicesPlace']['services_id'];
}

//phu hop voi
$pp = array();
foreach ($pur as $value) {
    $pp[] = $value['PlacesPurport']['purports_id'];
}
?>
<!--Thêm tính năng chia sẻ qua facebook-->
<div id="fb-root">

</div>
<script>
    $(function() {
        $("#submit_comment").click(function() {
            var con = $("#content_comment").val();
            var date = new Date();
            var places_id = <?php echo $place['Place']['id'] ?>;
            if (con != '') {
                $.ajax({
                    type: 'POST',
                    url: "../add_comment",
                    data: {
                        content: con,
                        places_id: places_id
                    }
                });

                var html = '<li class="list-group-item"> <p>'
                        + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() +
                        ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds() + '<br>' + con +
                        '</p></li>';
                $("#list_comment").append(html);
                var curr = $("#count_comment").html();
                curr = parseInt(curr) + 1;
                $("#count_comment").html(curr);
            }
        });

        //Khi người dùng bình chọn thích => tăng lượt like
        $("#like_page").click(function() {
            var places_id = <?php echo $place['Place']['id'] ?>;
            $.ajax({
                type: 'POST',
                url: "../add_like",
                data: {
                    places_id: places_id
                }
            });
        });

        //Đóng map view
        $("#close_map").click(function() {
            $("#direction_map").slideUp(3000);
        });
    });

</script>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId: '259464497577015',
            xfbml: true,
            status: true,
            version: 'v2.0'
        });
    };


    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    //Hiển thị thông tin chỉ đường cho người dùng
    function onShowMapDirection() {
        var map;
        var directionsDisplay;
        var directionsService;
        var stepDisplay;
        var markerArray = [];
        var currentPosition;
        var placePosition;
        var current;
        var place;
        
        //Làm rỗng vùng hiển thị
        $("#details_map").empty();
        $("#details_panel").empty();
        //Hiển thị map
        $("#direction_map").slideDown(3000);
        map = new google.maps.Map(document.getElementById('details_map'), {
            zoom: 8,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        //Lấy vị trí hiện tại của người dùng
        navigator.geolocation.getCurrentPosition(function(position) {
            currentPosition = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            placePosition = new google.maps.LatLng(<?php echo $place['Place']['latitude']; ?>, <?php echo $place['Place']['longitude']; ?>);
            map.setCenter(currentPosition);

            //Gửi thông tin lên server lấy thông tin về địa chỉ đó
            $.ajax({
                type: 'POST',
                url: "../get_address",
                data: {
                    latCurr: position.coords.latitude,
                    lgnCurr: position.coords.longitude,
                    latPlace:<?php echo $place['Place']['latitude']; ?>,
                    lgnPlace: <?php echo $place['Place']['longitude']; ?>
                },
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    if (data) {
                        current = data[0].addressCurr;
                        place = data[1].addressPlace;

                        // Tạo đối tượng tham chiếu tới map
                        var rendererOptions = {
                            map: map
                        }
                        directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions)
                        directionsDisplay.setPanel(document.getElementById('details_panel'));
                        // Đặt thông tin trên từng bước
                        stepDisplay = new google.maps.InfoWindow();
                        
                        //Kiểm tra kiểu di chuyển của người dùng lựa chọn
                        var type = $("#type").val();
                        var request;
                        if(type==2){
                            request = {
                            origin: current,
                            destination: place,
                            travelMode: google.maps.TravelMode.BICYCLING
                            };
                        }else if(type==3){
                            request = {
                            origin: current,
                            destination: place,
                            travelMode: google.maps.TravelMode.DRIVING
                            };
                        }else{
                            request = {
                            origin: current,
                            destination: place,
                            travelMode: google.maps.TravelMode.WALKING
                            };
                        }
                        directionsService = new google.maps.DirectionsService();
                        directionsService.route(request, function(response, status) {
                            if (status == google.maps.DirectionsStatus.OK) {
                                directionsDisplay.setDirections(response);
                                showSteps(response);
                            }
                        });

                        function showSteps(directionResult) {
                            var myRoute = directionResult.routes[0].legs[0];

                            for (var i = 0; i < myRoute.steps.length; i++) {
                                var marker = new google.maps.Marker({
                                    position: myRoute.steps[i].start_location,
                                    map: map
                                });
                                attachInstructionText(marker, myRoute.steps[i].instructions);
                                markerArray[i] = marker;
                            }
                        }
                        function attachInstructionText(marker, text) {
                            google.maps.event.addListener(marker, 'click', function() {
                                stepDisplay.setContent(text);
                                stepDisplay.open(map, marker);
                            });
                        }
                    }
                }
                
            });
        });

    }

</script>


<div class="content container" id="place">
    <div id="header">

        <h1 class="title "><?php echo $place['Place']['name']; ?></h1>
        <p><?php echo $this->Address->createAddress($place); ?></p>

        <div id="nav" class="col-md-12">
            <ul class="nav nav-pills">
                <li class="active"><a href="/CafeGarden/places/index">Trang chủ</a></li>
                <li><a href="#" onclick="onShowMapDirection()">Map</a></li>
            </ul>
        </div>
    </div><!-- End .header -->

    <!-- Hiển thị bản đồ chỉ đường cho người ở vị trí hiện tại-->
    <div id="direction_map" class="container-fluid wrap-item" style="display: none" >
        <div id="details_map" class="col-md-8" style="height: 500px;">

        </div>

        <div id="details_panel" class="col-md-4" style="height: 500px; overflow: auto">

        </div>
        <div class="row">
            <div class="col-md-8">
                Chọn phương tiện: 
                <select id="type" onchange="onShowMapDirection();">
                    <option value="1">Đi bộ</option>
                    <option value="2">Đi xe đạp</option>
                    <option value="3">Đi xe máy</option>
                </select>
                <button id="close_map" class="btn-sm">Đóng</button>
            </div>
            <div class="col-md-4">

            </div>
        </div>
    </div>


    <!--//////////////////////////////////////////////-->
    <div id="side" class="container-fluid wrap-item">
        <div id="side-img" class="col-md-8">
            <!-- Start WOWSlider.com -->
            <iframe src="/CafeGarden/places/slide/<?php echo $place['Place']['id']; ?>" style="width:620px;height:320px;max-width:100%;overflow:hidden;border:none;padding:0;margin:0 auto;display:block;" marginheight="0" marginwidth="0"></iframe>
            <!-- End WOWSlider.com -->
        </div>
        <div id="event" class="col-md-4 wrap-item">
            <div id="like_face" class="row">
                <div id="like_page" class="fb-like" data-href="http://localhost/CafeGarden/places/place/<?php echo $place['Place']['id'] ?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="true" width="300px"></div>
            </div>

            <h4>Sự kiện</h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <p>Khai trương quán cafe mừng giảm giá và tặng quà lưu niệm</p>
                </li>

            </ul>


        </div>
    </div>

    <div id="thucdon" class="container-fluid wrap-item">
        <div id="danhmuc" class="col-md-3">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Thực đơn</h3>
                </div>
                <ul class="nav nav-pills nav-stacked" style="max-width: 300px;">
                    <li class="active"><a href="#">Đồ uống</a></li>
                    <li ><a href="#">Cafe đá</a></li>
                    <li ><a href="#">Cafe sữa</a></li>
                    <li ><a href="#">Cafe nóng</a></li>
                </ul>
            </div>
        </div>
        <div id="danhsach" class="col-md-9 container-fluid">
            <div class="item col-md-4">
                <h4>Các món sinh tố</h4>
                <p>tất cả các món sinh tố trái cây, thơm ngon, bổ dưỡng cho những ngày hè</p>
                <p class="gia">15000 VNĐ</p>
            </div>
            <div class="item col-md-4">
                <h4>Các món sinh tố</h4>
                <p>tất cả các món sinh tố trái cây, thơm ngon, bổ dưỡng cho những ngày hè</p>
                <p class="gia">15000 VNĐ</p>
            </div>
            <div class="item col-md-4">
                <h4>Các món sinh tố</h4>
                <p>tất cả các món sinh tố trái cây, thơm ngon, bổ dưỡng cho những ngày hè</p>
                <p class="gia">15000 VNĐ</p>
            </div>
            <div class="item col-md-4">
                <h4>Các món sinh tố</h4>
                <p>tất cả các món sinh tố trái cây, thơm ngon, bổ dưỡng cho những ngày hè</p>
                <p class="gia">15000 VNĐ</p>
            </div>
        </div>
    </div><!-- End #thucdon -->

    <div id="chitiet" class="container-fluid wrap-item">
        <div class="ct-item col-md-4">
            <div class="panel  ">
                <div class="panel-heading">
                    <h3 class="panel-title">Giới thiệu</h3>
                </div>
                <div class="panel-body">
                    <p>
                        <?php echo $place['Place']['intro'] ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="ct-item col-md-4">
            <div class="panel ">
                <div class="panel-heading">
                    <h3 class="panel-title">Dịch vụ</h3>
                </div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="/CafeGarden/img/s1.jpg"/>
                                </div>
                                <div class="col-md-8">
                                    <p><b>Gọi đồ uống, gọi món</b></p>
                                    <p>Dịch vụ đặt trước đồ uống. Có thể phục vụ tại quán hoặc địa chỉ của khách hàng yêu cầu...</p>
                                </div>
                            </div>

                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="/CafeGarden/img/s2.jpg"/>
                                </div>
                                <div class="col-md-8">
                                    <p><b>Đặt bàn</b></p>
                                    <p>Đặt bàn, đặt phòng tổ chức tiệc sinh nhật, làm việc, hẹn hò...</p>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="/CafeGarden/img/s3.jpg"/>
                                </div>
                                <div class="col-md-8">
                                    <p><b>Chụp ảnh</b></p>
                                    <p>Với không giang đẹp lãng mạn. Quán là nới thích hợp cho các đôi uyên ương chụp ảnh cưới. Các bạn trẻ chụp ảnh lưu niệm...</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="ct-item col-md-4">
            <div class="panel ">
                <div class="panel-heading">
                    <h3 class="panel-title">Liện hệ</h3>
                </div>
                <div class="panel-body">
                    <h4>Số điện thoại: 01288833434</h4>

                    <!-- Dat thong tin ban do-->
                    <div id="gmap">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ***************************************Dich Vu ********************************* -->
    <div id="dichvu" class="container-fluid wrap-item">
        <div class="panel ">
            <div class="panel-heading">
                <h3 class="panel-title">Dịch vụ</h3>
            </div>
            <div class="panel-body">
                <ul class="list-group contairner-fluid">
                    <?php
                    foreach ($services as $service) {
                        $code = $service['Service']['code'];
                        $xhtml = '<li class="list-group-item col-md-4">';
                        if (array_search($code, $sp) === false) {
                            $xhtml .= '<input id="' . $code . '" class="css-checkbox" type="checkbox" disabled="disabled"/>';
                        } else {
                            $xhtml .= '<input id="' . $code . '" class="css-checkbox" type="checkbox" checked="checked" disabled="disabled"/>';
                        }
                        $xhtml .= '<label for="' . $service['Service']['code'] . '" name="' . $service['Service']['code'] . '" class="css-label">' . $service['Service']['name'] . '</label>';
                        $xhtml .= '</li>';
                        echo $xhtml;
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- ***************************************Phu Hop ********************************* -->
    <div id="phuhop" class="container-fluid wrap-item">
        <div class="panel ">
            <div class="panel-heading">
                <h3 class="panel-title">Phù hợp với</h3>
            </div>
            <div class="panel-body">
                <ul class="list-group contairner-fluid">
                    <?php
                    foreach ($purports as $purport) {
                        $code = $purport['Purport']['code'];
                        $xhtml = '<li class="list-group-item col-md-4">';
                        if (array_search($code, $pp) === false) {
                            $xhtml .= '<input id="' . $code . '" class="css-checkbox" type="checkbox" disabled="disabled"/>';
                        } else {
                            $xhtml .= '<input id="' . $code . '" class="css-checkbox" type="checkbox" checked="checked" disabled="disabled"/>';
                        }

                        $xhtml .= '<label for="' . $code . '" name="' . $code . '" class="css-label">' . $purport['Purport']['name'] . '</label>';
                        $xhtml .= '</li>';
                        echo $xhtml;
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <div id="ttphucvu" class="container-fluid wrap-item">

        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="panel ">
                    <div class="panel-heading">
                        <h3 class="panel-title">Thông tin phục vụ</h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group contairner-fluid">

                            <?php
                            $row = '<li class="list-group-item">';
                            $row .= 'Thời gian phục vụ: ' . $info[0]['Informations']['timeservice'];
                            $row .= '</li>';
                            $row .= '<li class="list-group-item">';
                            $row .= 'Ngày nghỉ: ' . $info[0]['Informations']['holiday'];
                            $row .= '</li>';
                            $row .= '<li class="list-group-item">';
                            $row .= 'Sức chứa: ' . $info[0]['Informations']['storage'];
                            $row .= '</li>';
                            $row .= '<li class="list-group-item">';
                            $row .= 'Giá trung bình: ' . $info[0]['Informations']['priceavg'];
                            $row .= '</li>';
                            $row .= '<li class="list-group-item">';
                            $row .= 'Phương thức thanh toán: ' . $info[0]['Informations']['methodpay'];
                            $row .= '</li>';
                            $row .= '<li class="list-group-item">';
                            $row .= 'Ngôn ngữ: ' . $info[0]['Informations']['lang'];
                            $row .= '</li>';
                            echo $row;
                            ?>

                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="panel" >

                    <div class="panel-heading">
                        <div class="row">
                            <p class="panel-title col-md-6"><span id="count_comment"><?php echo count($comments); ?></span> Bình luận</p>
                            <p class="panel-title col-md-6"><a id="add_comment">Viết bình luận</a></p>
                        </div>


                    </div>
                    <div class="panel-body">
                        <ul id="list_comment" class="list-group contairner-fluid" style="height: 140px; overflow: auto;">                          
                            <?php
                            foreach ($comments as $item) {
                                echo '<li class="list-group-item">';
                                echo $item['Comment']['created'] . "<br>";
                                echo $item['Comment']['content'];
                                echo '</li>';
                            }
                            ?>
                        </ul>

                        <div id="form_comment" style="display: none;">

                            <div class="col-md-12">
                                <textarea class="form-control" rows="1" id="content_comment"></textarea>
                                <span class="help-block" style="color:white">Chia sẻ cảm nhận của bạn về <?php echo $place['Place']['name'] ?></span>
                            </div>

                            <div class="col-md-2 col-md-offset-10">                                  
                                <button id="submit_comment" type="submit" class="btn btn-sm">Đăng</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div><!--end row-->

</div>

