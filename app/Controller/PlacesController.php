<?php

class PlacesController extends AppController {

    var $name = "Places";
    var $helpers = array('Html', 'Form', 'Js', 'Address');
    var $uses = array('Service', 'Place', 'Purport', 'ServicesPlace', 'PlacesPurport', 'Image', 'Informations', 'Comment');

    public function index() {
        $this->layout = "template";
        $this->set('title_for_layout', 'Cafe Garden | Welcome');
        $places = $this->Place->find('all', array(
            'order' => array('Place.id asc'),
            'limit' => 10,
            'offset' => 0
        ));

        $services = $this->Service->find('list', array(
            'fields' => array('Service.code', 'Service.name'),
                )
        );
        $purports = $this->Purport->find('list', array(
            'fields' => array('Purport.code', 'Purport.name'),
                )
        );

        //Lấy thông tin các tỉnh thành
        $provinces = $this->Place->find('all', array(
            'fields' => 'DISTINCT Place.province'
        ));

        $this->set("places", $places);
        $this->set("services", $services);
        $this->set("purports", $purports);
        $this->set("provinces", $provinces);
    }

    public function place($id) {
        $place = $this->Place->findById($id);
        $services = $this->Service->find('all');
        $purports = $this->Purport->find('all');
        $ser = $this->ServicesPlace->find('all', array(
            'conditions' => array(
                'ServicesPlace.places_id' => $place['Place']['code']
            )
        ));
        $pur = $this->PlacesPurport->find('all', array(
            'conditions' => array(
                'PlacesPurport.places_id' => $place['Place']['code']
            )
        ));

        $info = $this->Informations->find('all', array(
            'conditions' => array(
                'Informations.code' => $place['Place']['code']
            )
        ));

        //Tăng lượt view lên trong cơ sở dữ liệu
        $this->Place->read(null, $id);
        $this->Place->set('view', $place['Place']['view'] + 1);
        $this->Place->save();

        //Lấy thông tin các bài bình luận của quán
        $comments = $this->Comment->find('all', array(
            'conditions' => array(
                'Comment.places_id' => $id
            ),
            'order' => 'Comment.created desc'
        ));
        $this->layout = "template";
        $this->set('title_for_layout', $place['Place']['name'] . ' | Cafe Garden');
        $this->set('place', $place);
        $this->set('services', $services);
        $this->set('purports', $purports);
        $this->set('ser', $ser);
        $this->set('pur', $pur);
        $this->set('info', $info);
        $this->set('comments', $comments);
    }

    /**
     * Hàm tìm kiếm dữ liệu khi người dùng lựa chọn tiêu chí tìm kiếm
     */
    public function advance_search() {
        $this->autoRender = false;
        $this->request->onlyAllow('ajax');

        //Lấy thông tin client gửi lên
        $start = $_POST['start'];
        $ser = $_POST['ser'];
        $pur = $_POST['pur'];
        $street = $_POST['street'];
        $pro = $_POST['pro'];
        $cat = $_POST['cat'];
        $dist = $_POST['dist'];
        $a = $_POST['a'];
        $orderby = $_POST['orderby'];
        $limit = $_POST['limit'];

        //Kiểm tra thông tin của các biến gửi tới và tách mảng giá trị
        $place_id_ser = array();
        $place_id_pur = array();
        $places = array();

        if ($ser != "") {
            $arr_ser = explode(",", $ser);
            //Tìm tất cả các quán có dịch vụ tương ứng 

            $place_ser = $this->ServicesPlace->find('all', array(
                'conditions' => array(
                    'ServicesPlace.services_id' => $arr_ser
                ),
                'fields' => 'DISTINCT ServicesPlace.places_id',
                'order' => 'ServicesPlace.id asc',
            ));
            //Duyệt qua tất cả các id của quán
            foreach ($place_ser as $item) {
                $place_id_ser[] = $item['ServicesPlace']['places_id'];
            }
        }
        if ($pur != "") {
            $arr_pur = explode(",", $pur);

            //Đếm số phần tử của mảng các dịch vụ được chọn
            $num_pur = count($arr_pur);
            //Tìm tất cả các quán có mục đích tương ứng
            $place_pur = $this->PlacesPurport->find('all', array(
                'conditions' => array(
                    'PlacesPurport.purports_id' => $arr_pur,
                ),
                'fields' => 'DISTINCT PlacesPurport.places_id',
                'order' => 'PlacesPurport.id asc'
            ));
            //Duyệt qua tất cả các id của quán
            foreach ($place_pur as $item) {
                $place_id_pur[] = $item['PlacesPurport']['places_id'];
            }
        }


        //Kiểm tra trường pro
        if ($pro != "") {
            //Nếu mà hai mảng đều rỗng thì tìm các quán với giá trị đường phố nhập vào
            if ((count($place_id_ser) == 0) && (count($place_id_pur) == 0)) {
                $places = $this->Place->find('all', array(
                    'conditions' => array(
                        'Place.street LIKE' => "%$street%",
                        'Place.district LIKE' => "%$dist%",
                        'Place.name LIKE' => "%$a%",
                        'Place.province' => $pro,
                    ),
                    'order' => 'Place.' . $orderby,
                    'limit' => $limit,
                    'offset' => $start
                ));
            } else if ((count($place_id_ser) != 0) && (count($place_id_pur) == 0)) {
                $places = $this->Place->find('all', array(
                    'conditions' => array(
                        'Place.code' => $place_id_ser,
                        'Place.street LIKE' => "%$street%",
                        'Place.district LIKE' => "%$dist%",
                        'Place.name LIKE' => "%$a%",
                        'Place.province' => $pro,
                    ),
                    'order' => 'Place.' . $orderby,
                    'limit' => $limit,
                    'offset' => $start
                ));
            } else if ((count($place_id_pur) != 0) && (count($place_id_ser) == 0)) {
                $places = $this->Place->find('all', array(
                    'conditions' => array(
                        'Place.code' => $place_id_pur,
                        'Place.street LIKE' => "%$street%",
                        'Place.district LIKE' => "%$dist%",
                        'Place.name LIKE' => "%$a%",
                        'Place.province' => $pro,
                    ),
                    'order' => 'Place.' . $orderby,
                    'limit' => $limit,
                    'offset' => $start
                ));
            } else {//Lấy giao của hai mảng id của quán
                $arr_pur_ser = array_intersect($place_id_pur, $place_id_ser);
                $places = $this->Place->find('all', array(
                    'conditions' => array(
                        'Place.code' => $arr_pur_ser,
                        'Place.street LIKE' => "%$street%",
                        'Place.district LIKE' => "%$dist%",
                        'Place.name LIKE' => "%$a%",
                        'Place.province' => $pro,
                    ),
                    'order' => 'Place.' . $orderby,
                    'limit' => $limit,
                    'offset' => $start
                ));
            }
        } else {
            //Nếu mà hai mảng đều rỗng thì tìm các quán với giá trị đường phố nhập vào
            if ((count($place_id_ser) == 0) && (count($place_id_pur) == 0)) {
                $places = $this->Place->find('all', array(
                    'conditions' => array(
                        'Place.street LIKE' => "%$street%",
                        'Place.district LIKE' => "%$dist%",
                        'Place.name LIKE' => "%$a%",
                    ),
                    'order' => 'Place.' . $orderby,
                    'limit' => $limit,
                    'offset' => $start
                ));
            } else if ((count($place_id_ser) != 0) && (count($place_id_pur) == 0)) {
                $places = $this->Place->find('all', array(
                    'conditions' => array(
                        'Place.code' => $place_id_ser,
                        'Place.street LIKE' => "%$street%",
                        'Place.district LIKE' => "%$dist%",
                        'Place.name LIKE' => "%$a%",
                    ),
                    'order' => 'Place.' . $orderby,
                    'limit' => $limit,
                    'offset' => $start
                ));
            } else if ((count($place_id_pur) != 0) && (count($place_id_ser) == 0)) {
                $places = $this->Place->find('all', array(
                    'conditions' => array(
                        'Place.code' => $place_id_pur,
                        'Place.street LIKE' => "%$street%",
                        'Place.district LIKE' => "%$dist%",
                        'Place.name LIKE' => "%$a%",
                    ),
                    'order' => 'Place.' . $orderby,
                    'limit' => $limit,
                    'offset' => $start
                ));
            } else {//Lấy giao của hai mảng id của quán
                $arr_pur_ser = array_intersect($place_id_pur, $place_id_ser);
                $places = $this->Place->find('all', array(
                    'conditions' => array(
                        'Place.code' => $arr_pur_ser,
                        'Place.street LIKE' => "%$street%",
                        'Place.district LIKE' => "%$dist%",
                        'Place.name LIKE' => "%$a%",
                    ),
                    'order' => 'Place.' . $orderby,
                    'limit' => $limit,
                    'offset' => $start
                ));
            }
        }


        return json_encode($places);
    }

    /**
     * Hàm lấy thông tin các địa điểm gửi về khi search map
     */
    function search_map() {
        $this->autoRender = false;
        $this->request->onlyAllow('ajax');

        $pleaces_map = $this->Place->find('all');
        return json_encode($pleaces_map);
    }

    /**
     * Hàm lấy các quận của một tỉnh thành
     */
    function search_province() {
        $this->autoRender = false;
        $this->request->onlyAllow('ajax');

        //Lấy dữ liệu gửi từ client
        $pro = $_POST['pro'];
        $districts = $this->Place->field('all', array(
            'conditions' => array(
                'Place.province' => $pro,
            ),
            'fields' => 'DISTINCT Place.district'
        ));

        return json_encode($districts);
    }

    /**
     * Hàm tìm tên tất cả các quận
     */
    function search_district() {
        $this->autoRender = false;
        $this->request->onlyAllow('ajax');
        $dist = $_POST['content'];
        $districts = $this->Place->find('all', array(
            'conditions' => array(
                'Place.district LIKE' => "%$dist%"
            ),
            'fields' => 'DISTINCT Place.district'
        ));

        return json_encode($districts);
    }

    /**
     * Slide for place 
     */
    function slide($id) {
        $this->layout = 'empty_layout';
        $place = $this->Place->findById($id);
        $images = $this->Image->find('all', array(
            'conditions' => array(
                'Image.code' => $place['Place']['code']
            )
        ));
        $this->set('place', $place);
        $this->set('images', $images);
    }

    /**
     * Xử lý thêm comment cho một địa điểm
     */
    function add_comment() {
        $this->autoRender = false;
        $this->request->onlyAllow('ajax');

        $content = $_POST['content'];
        $places_id = $_POST['places_id'];

        $this->Comment->create();
        $data = array('content' => $content, 'places_id' => $places_id, 'created' => getdate());
        $this->Comment->save($data);
    }

    /**
     * Thêm lượt like cho một trang có id gửi tới
     */
    function add_like() {
        $this->autoRender = false;
        $this->request->onlyAllow('ajax');

        $places_id = $_POST['places_id'];
        $place = $this->Place->findById($places_id);

        //Tăng lượt view lên trong cơ sở dữ liệu
        $this->Place->read(null, $places_id);
        $this->Place->set('numlike', $place['Place']['numlike'] + 1);
        $this->Place->save();
    }

    /**
     * Tìm địa chỉ với long, lat gửi từ client
     */
    function get_address() {
        $this->autoRender = false;
        $this->request->onlyAllow('ajax');
        
        $latCurr = $_POST['latCurr'];
        $lgnCurr = $_POST['lgnCurr'];
        $latPlace = $_POST['latPlace'];
        $lgnPlace = $_POST['lgnPlace'];

        $arr = array();
        
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($latCurr) . ',' . trim($lgnCurr) . '&sensor=false';
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        if ($status == "OK")
            array_push ($arr, array('addressCurr'=>$data->results[0]->formatted_address));
        
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($latPlace) . ',' . trim($lgnPlace) . '&sensor=false';
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        if ($status == "OK")
            array_push ($arr, array('addressPlace'=>$data->results[0]->formatted_address));
        
        return json_encode($arr);
    }

}
