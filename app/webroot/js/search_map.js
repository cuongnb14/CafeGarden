
/**
 * convert degree to radian
 */
function toRad(degree) {
    return (degree * Math.PI) / 180;
}

/**
 * calculate the distance between 2 point base on geographic coordinate
 */
function calDistance(lat1, lng1, lat2, lng2) {
    var R = 6371; // km
    var dLat = toRad(lat2 - lat1);
    var dLng = toRad(lng2 - lng1);
    var lat1 = toRad(lat1);
    var lat2 = toRad(lat2);

    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.sin(dLng / 2) * Math.sin(dLng / 2) * Math.cos(lat1) * Math.cos(lat2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c;
    return d;
}
/**
 * get range from user
 */
function getRadius() {
    var tmpRad = document.getElementById("radius");
    var rad = tmpRad.elements[0].value;
    return rad;
}

/**
 * change the zoom lever according to radius
 */
function getZoom(rad) {
    if (rad <= 1) {
        return 17;
    }
    else if (rad <= 2) {
        return 16;
    }
    else if (rad <= 4) {
        return 15;
    }
    else if (rad <= 7) {
        return 14;
    }
    else if (rad <= 10) {
        return 13;
    }
    else if (rad <= 15) {
        return 12;
    }
    else
        return 8;
}


function handlerDataReceivMap(data) {
    var image = {
        url: '/CafeGarden/img/red_cafe.png',
        //This marker is x pixels wide by y pixels tall.
        size: new google.maps.Size(37, 48),
        //The origin for this image is 0,0.
        origin: new google.maps.Point(0, 0),
        //The anchor for this image is the base of the flagpole at 0,32.
        anchor: new google.maps.Point(0, 32)
    };

    var locations = data;

    var map = new google.maps.Map(document.getElementById('google_canvas'), {
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var geolocate;
    navigator.geolocation.getCurrentPosition(function(position) {
        geolocate = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        map.setCenter(geolocate);
        var marker;
        marker = new google.maps.Marker({
            position: geolocate,
            map: map
        });
        var infowindow = new google.maps.InfoWindow();
        var i = 0;

        //Lấy bán kính cần xét
        var radius = $("#radius").val();

        map.setZoom(12);
        /**
         *Draw circle in the map 
         *radius of circle = radius
         */
        var circleOptions = {
            strokeColor: 'blue',
            strokeOpacity: 0.5,
            strokeWeight: 1,
            fillColor: 'yellow',
            fillOpacity: 0.1,
            map: map,
            center: geolocate,
            radius: radius * 1000
        };

        // Add the circle for this city to the map.
        cityCircle = new google.maps.Circle(circleOptions);

        /**
         * Lấy danh sách long, lat
         */
        var k =0;
        for (var x in locations) {
            for (var y in locations[x]) {
                var distance = calDistance(geolocate.lat(), geolocate.lng(), locations[x][y].latitude, locations[x][y].longitude);
                if (distance < radius) {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[x][y].latitude, locations[x][y].longitude),
                        animation: google.maps.Animation.DROP,
                        icon: image,
                        title: locations[x][y].name,
                        map: map
                    });
                }
                google.maps.event.addListener(marker, 'click', (function(marker,x) {
                    return function() {
                        var str = locations[x][y].houseno + ', ' + locations[x][y].street + ', ' + locations[x][y].district + ', ' + locations[x][y].province + ', ' + locations[x][y].national;
                        str = '<div><a href ="/CafeGarden/places/place/' + locations[x][y].id + '"><h3>' + locations[x][y].name + "<h3></a></div>" + str + "... ";

                        infowindow.setContent(str);
                        infowindow.setOptions({maxWidth: 250});

                        infowindow.open(map, marker);
                    }
                })(marker,k));
                k++;
            }

        }
    });
}

$(function() {

    $("#search_place_near").click(function() {

        $("#xemthemmap").show();
        $("#xemthemmap").css({height:"30px"});
        
        //Đặt lại cờ flag_map
        flag_map = true;

        //Ẩn danh sách các quán
        $(".show_more_place").empty();

        //Tắt sự kiện loading
        $status = false;

        //Định dạng thẻ google_canvas
        $("#google_canvas").css({height: "500px"});
        //Gửi thông tin yêu cầu lên server
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
            success: function(data, textStatus, jqXHR) {
                if (data && flag_map) {
                    handlerDataReceivMap(data);
                }
            },
            beforeSend: function(xhr) {
                $("#loading").show();
            },
            complete: function() {
                $("#loading").hide();
            }
        });
    });
    
    //Bắt sự kiện người dùng nhấn nút xem thêm trên map
    $("#bt_xem_them_map").click(function(){
        limit += 3;
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
            success: function(data, textStatus, jqXHR) {
                if (data && flag_map) {
                    handlerDataReceivMap(data);
                }
            },
            beforeSend: function(xhr) {
                $("#loading").show();
            },
            complete: function() {
                $("#loading").hide();
            }
        });
    });
});

