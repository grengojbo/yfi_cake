<?php

class CmpNasComponent extends Object {

    //----Component with common functions related to NAS Devicves------------------------------------------------
    //---NOTE: This idea came in late - so not all controllers use it, newer controllers will make use of it -----
    //---This was to allow easy third party integrations ---------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------

    //---The controller who uses the component needs to use the following models:
    //-> 'Na','NaRealm','Realm'

    var $components = array('Session','Rights','Formatter');

    function initialize(&$controller) {
        // saving the controller reference for later use
        $this->controller =& $controller;
    }

     function getRealmsForNa($na_id){
    //Determine the realms for a NAS device and if the current user (AccessProvider) has rights to view it

        $qr =$this->controller->NaRealm->find('all',array('conditions' => array('NaRealm.na_id' => $na_id)));

        $realms_string ='(all)';        //Default if none are defined
        $show_flag = true;

        $count = 0;
        $loop_trigger = false;
        foreach($qr as $item){

            $this_realm     = $item['Realm']['name'];
            $realm_id       = $item['Realm']['id'];
            if($this->Rights->CheckRealmIdAllowed($realm_id)){    
                if($count > 0){
                    $realms_string   = $realms_string.'<br> '.$this_realm;
                }else{
                    $realms_string = $this_realm;
                }
                $count++;
            }
            $loop_trigger = true;   //There was realms tied to this template
        }

        if(($loop_trigger)&&($realms_string == '(all)')){   //Check it there were realms tied to this and whether all were rejected 

            $auth_data = $this->Session->read('AuthInfo');
            Configure::load('yfi'); 
            if($auth_data['Group']['name'] == Configure::read('group.admin')){
                $show_flag = true;
            }else{
                $show_flag = false;
            }
        }

        $ret_arr = array('show'=>$show_flag,'available_to' =>$realms_string);
        return $ret_arr;
    }

    //Get the stations connected to an AP
    function getStations($nasname){
        return $nasname;
    }

}

?>
