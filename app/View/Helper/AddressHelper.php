<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AddressHelper extends AppHelper{
    
    function createAddress($place){
        return $place['Place']['houseno'].", đường ".$place['Place']['street'].", ".$place['Place']['ward'].", ".$place['Place']['district'].", ".$place['Place']['province'].", ".$place['Place']['national'];
    }
}

?>
