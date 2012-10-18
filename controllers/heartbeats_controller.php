<?php

class HeartbeatsController extends AppController {
    var $name       = 'Heartbeats';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component
    var $uses       = array('Heartbeat','Na','Action');

     function beforeFilter() {
       $this->Auth->allow('json_hello','active_check');
    }

    function active_check($mac){
        $this->layout = 'text';
        //Check if the MAC is in the correct format
        $pattern = '/^([0-9a-fA-F]{2}[-]){5}[0-9a-fA-F]{2}$/i';
        if(preg_match($pattern, $mac)< 1){
            $error = "ERROR: MAC missing or wrong";
            $this->set('error',$error);
            return;
        }

        //We use a convention of 08-00-27-56-22-0B_ce5b65b780ae42d937294b2aa166d608 in the community field
        //in the nas table. We specify a MAC with an underscore initially and when the device register itself
        //We add the device generated password
        $r = $this->Na->find('first',array('conditions' =>array('Na.community' => $mac)));

        if($r == ''){
            $return_string = "HEARTBEAT=NO";
        }else{
            $return_string = "HEARTBEAT=".$r['Na']['nasname'];
        }

        //--Return the actions (if required) ---
        $this->set('return_string',$return_string);
        //--------------------------------------
    }

    function json_hello($mac){

        //$this->layout = 'ajax';
        $this->layout = 'text';
        //Check if the MAC is in the correct format
        $pattern = '/^([0-9a-fA-F]{2}[-]){5}[0-9a-fA-F]{2}$/i';
        if(preg_match($pattern, $mac)< 1){
            $error = "ERROR: MAC missing or wrong";
            $this->set('error',$error);
            return;
        }

        //We need to do a few tests
        //1.) Check if the MAC is registered in the heartbeats table
        $qr = $this->Heartbeat->find('first',array('conditions' => array('Na.community' => $mac)));

        if($qr == ''){ 
            $error = "ERROR: MAC not defined correct -OR- NAS type not correct";
            $this->set('error',$error);
            return;
        }else{
            $d['Heartbeat']['id'] = $qr['Heartbeat']['id'];
            $this->Heartbeat->save($d);
            //Get the nas's ID in odred to find if there are any avaiting commands for it.
            $nas_id = $qr['Heartbeat']['na_id'];
        }
      
        //We also need to see if there are any actions awaiting for this NAS.....
        $qr = $this->Action->find('all',array('conditions' => array('Action.na_id' => $nas_id, 'Action.status' => 'awaiting')));
        //Loop this results to build the return string
        $return_string = "";
        foreach($qr as $item){

            $id         = $item['Action']['id'];
            $action     = $item['Action']['action'];
            $command    = $item['Action']['command'];
            $return_string = $return_string."unique_id: $id\naction: $action\n$command\n";
        }

        //Change all the 'awaiting' status now to 'fetched'
        $this->Action->updateAll(
            array('Action.status' => "'fetched'"),
            array('Action.status' => 'awaiting','Action.na_id' => $nas_id)
        );

        //--Return the actions (if required) ---
        $this->set('return_string',$return_string);
        //--------------------------------------
    }

}
?>
