<?php

class HeartbeatsController extends AppController {
    var $name       = 'Heartbeats';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component
    var $uses       = array('Na','Heartbeat');

     function beforeFilter() {
       $this->Auth->allow('json_hello');
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

        }
        
      //  print_r($qr);

    }

}
?>
