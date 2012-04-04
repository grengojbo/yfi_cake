<?php

class Realm extends AppModel {


    public function beforeSave() {
        //Check if lon and lat is in the array 
        if ((isset($this->data[$this->alias]['lon']))&&(isset($this->data[$this->alias]['lat']))) {
            //Confirm that they BOTH are empty!
            if(($this->data[$this->alias]['lon'] == '')&&($this->data[$this->alias]['lat'] == '')){
                //See if we can get some location detail for this REALM
                $this->_get_location_from_address();
            }
        }
        return true;
    }

    
     var $hasMany = array(
                    'UserRealm' => array(
                    'className' => 'UserRealm',
                    'order' => 'UserRealm.created DESC'
                    ),
                    'Photo' => array(
                        'className' => 'Photo',
                        'order'     => 'Photo.created DESC'
                    )
        );

    private function _get_location_from_address() {

        //Before we can attempt to get lng and lat values we need to confirm that ALL the required Address fields are present
        if (    (isset($this->data[$this->alias]['street_no']))&&
                (isset($this->data[$this->alias]['street']))&&
                (isset($this->data[$this->alias]['town_suburb']))&&
                (isset($this->data[$this->alias]['city']))){

            if( ($this->data[$this->alias]['street_no'] != '')&&
                ($this->data[$this->alias]['street'] != '')&&
                ($this->data[$this->alias]['town_suburb'] != '')&&
                ($this->data[$this->alias]['city'] != '')){


                //Get the url and country from the settings
                Configure::load('yfi');
                $url        = Configure::read('geocode.url');
                $country    = Configure::read('geocode.country_code');

                
                $no     =  $this->data[$this->alias]['street_no'];
                $str    =  urlencode($this->data[$this->alias]['street']);
                $town   =  urlencode($this->data[$this->alias]['town_suburb']);
                $city   =  urlencode($this->data[$this->alias]['city']);
                $a_string = $no.'+'.$str.',+'.$town.',+'.$city.',+'.$country;

                CakeLog::write('debug', "The Address string is ".$a_string);
                App::import('Core', 'HttpSocket');
                $HttpSocket = new HttpSocket();

                $response = $HttpSocket->get($url,array(
                   // 'address'   => '619+Graniet+Street,+Silverton,+Pretoria,+ZA',
                    'address'   => $a_string,
                    'sensor'    => 'false'
                ));

                if(!empty($response)){

                    $geocode = json_decode($response);
                    $lat = $geocode->results[0]->geometry->location->lat;
                    $lng = $geocode->results[0]->geometry->location->lng; 
                    $formatted_address = $geocode->results[0]->formatted_address;
                    $geo_status = $geocode->status;
                    $location_type = $geocode->results[0]->geometry->location_type;

                    CakeLog::write('debug', "The Status is ".$geo_status);
                    CakeLog::write('debug', "The Lat value is ".$lat);
                    CakeLog::write('debug', "The Lon value is ".$lng);

                    $this->data[$this->alias]['lon'] = $lng;
                    $this->data[$this->alias]['lat'] = $lat;
                }
            }
        }else{

            return;
        }
    }
  
}

?>
