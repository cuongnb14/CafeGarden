
<div class="content" id="index">
    <div class="sidebar col-md-3">

        <div class="panel ">
            <div class="panel-heading">
                <h3 class="panel-title">Tỉnh thành</h3>
            </div>
            <div class="panel-body mouse_enter">
                <h4>Chọn tỉnh thành</h4>
                <select class="form-control" id="select_province">
                    <option value="">--Tất cả--</option>
                    <?php
                    /*
                     * Chèn các tỉnh thành trong database
                     */
                    foreach ($provinces as $province) {
                        echo "<option value=\"" . $province['Place']['province'] . "\">" . $province['Place']['province'] . "</option>";
                    }
                    ?>
                </select>
                <div class="area">
                    <ul id="dis-list" class="list-group" >
                        <!--
                        <li class="list-group-item">
                            <span class="badge">14</span>
                            <input id="demo_box_1" class="css-checkbox" type="checkbox" />
                            <label for="demo_box_1" name="demo_lbl_1" class="css-label">Đống Đa</label>
                        </li>
                        <li class="list-group-item">
                            <span class="badge">12</span>
                            <input id="demo_box_2" class="css-checkbox" type="checkbox" />
                            <label for="demo_box_2" name="demo_lbl_2" class="css-label">Cầu Giấy</label>
                        </li>
                        <li class="list-group-item">
                            <span class="badge">3</span>
                            <input id="demo_box_3" class="css-checkbox" type="checkbox" />
                            <label for="demo_box_3" name="demo_lbl_3" class="css-label">Hai Bà Trưng</label>
                        </li>
                        <li class="list-group-item">
                            <span class="badge">2</span>
                            <input id="demo_box_4" class="css-checkbox" type="checkbox" />
                            <label for="demo_box_4" name="demo_lbl_4" class="css-label">Long Biên</label>
                        </li>
                        -->
                    </ul>

                </div>
            </div>
        </div>

        <div class="panel ">
            <div class="panel-heading">
                <h3 class="panel-title">Tìm theo đường/phố</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="search_street" placeholder="Tên đường/phố">
                </div>
            </div>
        </div>

        <div class="panel ">
            <div class="panel-heading">
                <h3 class="panel-title">Dịch vụ</h3>
            </div>
            <div class="panel-body mouse_enter">
                <ul class="list-group">

                    <?php
                    //Chèn thông tin dịch vụ vào
                    $mark = 0;
                    foreach ($services as $key => $service) {
                        $mark += 1;
                        if ($mark >= 6) {
                            echo '<li class="list-group-item hiden-ser" value="more_service">';
                        } else {
                            echo '<li class="list-group-item">';
                        }
                        echo '<input id="' . $key . '" class="css-checkbox" type="checkbox" value="ser"/>';
                        echo '<label class="css-label">' . $service . '</label>';
                        echo '</li>';
                    }
                    ?>
                </ul>         

            </div>
            <div class="panel-body">
                <p id="service_show" class="button_show">Xem thêm >></p>
            </div>
        </div>

        <div class="panel ">
            <div class="panel-heading">
                <h3 class="panel-title">Mục đích</h3>
            </div>
            <div class="panel-body mouse_enter">
                <ul class="list-group">
                    <?php
                    //Chèn thông tin mục đích của quán
                    $mark = 0;
                    foreach ($purports as $key => $purport) {
                        $mark += 1;
                        if ($mark >= 6) {
                            echo '<li class="list-group-item hiden-pur" value="more_pur">';
                        } else {
                            echo '<li class="list-group-item">';
                        }
                        echo '<input id="' . $key . '" class="css-checkbox" type="checkbox" value="pur"/>';
                        echo '<label class="css-label">' . $purport . '</label>';
                        echo '</li>';
                    }
                    ?>
                </ul>

            </div>
            <div class="panel-body">
                <p id="purport_show" class="button_show">Xem thêm >></p>
            </div>
        </div>
        <!--
                <div class="panel ">
                    <div class="panel-heading">
                        <h3 class="panel-title">Khoảng cách(km)</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <input type="text" class="form-control" id="inputDefault" placeholder="km">
                        </div>
                    </div>
                </div>
        -->
        <div class="panel ">
            <div class="panel-heading">
                <h3 class="panel-title">Loại quán</h3>
            </div>
            <div class="panel-body">
                <select class="form-control" id="select">
                    <option>--Tất cả--</option>
                    <option>Bình dân</option>
                    <option>Tiêu chuẩn</option>
                    <option>Sang trọng</option>	
                </select>
            </div>
        </div>

        <div class="panel ">
            <div class="panel-heading">
                <h3 class="panel-title">Loại món</h3>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <input id="s1" class="css-checkbox" type="checkbox" />
                        <label for="s1" name="s1" class="css-label">Internet Wifi</label>
                    </li>
                    <li class="list-group-item">
                        <input id="s2" class="css-checkbox" type="checkbox" />
                        <label for="s2" name="s2" class="css-label">Giữ xe miễn phí</label>
                    </li>
                    <li class="list-group-item">
                        <input id="s3" class="css-checkbox" type="checkbox" />
                        <label for="s3" name="s3" class="css-label">Chỗ trẻ em chơi</label>
                    </li>
                    <li class="list-group-item">
                        <input id="s4" class="css-checkbox" type="checkbox" />
                        <label for="s4" name="s4" class="css-label">Bàn bia</label>
                    </li>
                </ul>
            </div>
        </div>

        <div class="panel ">
            <div class="panel-heading">
                <h3 class="panel-title">Giờ phục vụ</h3>
            </div>
            <div class="panel-body">
                <select class="form-control" id="select">
                    <option>--Tất cả--</option>
                    <option>01:00</option>
                    <option>02:00</option>
                </select>
            </div>

        </div>

    </div><!-- End sidebar -->



    <div class="main-content col-md-9">


        <div class="panel">
            <div class="panel-heading container-fluid">
                <!--<h3 class="panel-title col-md-6">Có <span class="result-total">12/1200</span> kết quả</h3>-->
                            <div class="col-md-4 navbar-right">
                                <select class="form-control input-sm" id="orderby">
                                    <option value="id asc">----Sắp xếp----</option>
                                    <option value="view desc">Xem nhiều nhất</option>
                                    <option value="numlike desc">Nhiều người thích nhất</option>
                                    <option value="vote desc">Đánh giá cao nhất</option>
                                </select>
                            </div>

                            </div>

                            <!--Hiển thị danh sách các tiêu trí tìm kiếm-->
                            <div class="container-fluid view_detail">

                                <div class="list_search">

                                </div>

                                <div class="form-group col-md-4 navbar-right">                                 
                                    <div class="input-group">      
                                        <input id="radius" type="number" class="form-control input-sm" placeholder="km">                                      
                                        <span class="input-group-btn">
                                            <button id="search_place_near" class="btn btn-sm" type="button" title="Xem trên bản đồ">Tìm quán gần bạn</button>

                                            <button id="search_list"type="button" class="btn btn-sm">
                                                <img src="/CafeGarden/css/list_detail.png" style="height: 19px; width: 19px" title="Xem theo danh sách"/>
                                            </button>            
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-body container-fluid search_map">

                            </div>

                            <div id="google_canvas" class="panel-body container-fluid show_more_place">
                                <!-- ************************************************************************************************ -->                          
                                <?php
                                foreach ($places as $place) {
                                    $address = $place['Place']['houseno'] . ", " . $place['Place']['street'] . ", " . $place['Place']['district'] . ', ' . $place['Place']['province'] . ", " . $place['Place']['national'];
                                    $xhtml = "";
                                    $intro = trim($place['Place']['intro']);
                                    
                                    $xhtml .= '<div class="row">
                                            <div class="col-xs-6 col-md-3">
                                                <a href="/CafeGarden/places/place/' . $place['Place']['id'] . '" class="thumbnail">
							 ' . $this->Html->image('front/' . $place['Place']['image'], array('alt' => 'CakePHP')) . '
                                                </a>
                                            </div>
                                                    <div class="details col-md-7 col-xs-6">
                                                        <a href="/CafeGarden/places/place/' . $place['Place']['id'] . '"><h3>' . $place['Place']['name'] . '</h3></a>
                                                        <p class="address">' . $address . '</p>
							<p class="decription">
                                                            ' . $intro . '...
							</p>
                                                    </div>
                                                    <div class="rating col-md-2 col-xs-12">
							<span class="point">' . $place['Place']['vote'] . '.0</span>
							<ul>
                                                        <li><span>' . $place['Place']['numlike'] . '</span> lượt thích</li>
							<li><span>' . $place['Place']['view'] . '</span> lượt xem</li>
							</ul>
                                                    </div>
				    	</div>';
                                    echo $xhtml;
                                }
                                ?>

                            </div>
                            <div id="xemthemmap" style="display: none;">
                                <button id="bt_xem_them_map" class="btn btn-sm">Xem thêm</button>
                            </div>

                            <div id="loading">
                                Đang tải dữ liệu...
                            </div>              
                            </div>

                            </div><!-- End main-content -->
                            </div><!-- End content -->

