<?php

class NasmonitorShell extends Shell {

    //-----------Call this shell like so:---------------------------------
    //"/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake nasmonitor";
    // This script will run through cron and can be used to notify the owner of the AP
    // When it is down ------------------------------------------------------
    // Run it as www-data because of the temp files belonging to www-data
    //---------------------------------------------------------------------


    //--------------------------------------------------------
    //---PREDEFINED VARIABLES---------------------------------
    //--------------------------------------------------------
    //Models that we will be using
    var $uses       = array('NaState','Na');

    //For debug info
    var $debug_flag = true;

    var $infoArray = array();
    //=============END OF SECTION ========================


    //--------------------------------------------------------
    //---Load models at the start-----------------------------
    //--------------------------------------------------------
    function initialize()
    {
        $this->_loadModels();

        //-----------------------------------------------------------
        //--Load configuration variables ----
        Configure::load('yfi');
        //-----------------------------------------

        $this->dead_after    = Configure::read('heartbeat.dead_after');
        //----------------------------------------
    }
    //==============END OF SECTION =======================

    

    function main(){

        //We can tel the monitor program to only test heartbeat devices
       
        //---Initialization code------------------------------
        App::import('Core', array('View', 'Controller'));
        App::import('Controller', array('Nas'));
        $this->NasController = new NasController();
        //$this->NasController->loadModel();
        $this->NasController->constructClasses();

        if(array_key_exists('only_heartbeat',$this->params)){
            print("===Only Heartbeat devices===\n");
            $this->_heartbeat();
            return;
        }



        //First discover the devices which are not heartbeat devices that needs monitoring
        $qr = $this->NasController->Na->find('all',array('conditions' => array('Na.monitor' => '1','Na.type !=' => 'CoovaChilli-NAT' )));
        foreach($qr as $item){

            //Get the last state
            $last_state = 'new';
            if(count($item['NaState']) > 0){
                $last_state = $item['NaState'][0]['state'];
            }
            $nasname    = $item['Na']['nasname'];
            $id         = $item['Na']['id'];
            $this->_test_device($last_state,$nasname,$id);
        }

        //Discover heartbeat devices
        $this->_heartbeat();
    }


    function _heartbeat(){

        //Discover devices which are heartbeat devices that needs monitoring
        $qr = $this->NasController->Heartbeat->find('all',array('conditions' => array('Na.monitor' => '1','Na.type' => 'CoovaChilli-NAT' )));
        foreach($qr as $item){

            print "Consider a NAS device dead after ".$this->dead_after." seconds of no heartbeat\n"; 
            //If we consider the amount of seconds of the last heartbeat since the epoch and add the dead_after seconds to it, 
            //It should be more than now, else it has fallen behind and must be marked dead....
            //!!!!Remember then that it is important that the heartbeat should run more often than this cron script!!!!!
            
            //If the created and modified time is equal; the device is dead
            if($item['Heartbeat']['created'] == $item['Heartbeat']['modified']){
                $state = 0;     
            }else{
                $alive_until = strtotime($item['Heartbeat']['modified'])+$this->dead_after;     //Get the unix stamp for given date
                if($alive_until > time()){
                    $state = 1;
                }else{
                    $state = 0;
                }
            }

            //Check the last state
            $sq = $this->NasController->Na->find('first',array('conditions'=> array('Na.id' => $item['Na']['id'])));
            //Get the last state
            $last_state = 'new';
            if(count($sq['NaState']) > 0){
                $last_state = $sq['NaState'][0]['state'];
            }
            //Add an entry to the state table
            if($last_state != $state){
                $d['NaState']['id']        = '';
                $d['NaState']['na_id']     = $item['Na']['id'];
                $d['NaState']['state']     = $state;
                $this->NaState->save($d);
            } 
        }   
    }

    function _test_device($last_state, $nasname,$id){

        $ping_count = '1';
        print "Testing $nasname $last_state $id\n";
        $feedback = array();
        $state = '0';
        exec("ping -c $ping_count -q $nasname",$feedback);
        foreach($feedback as $line){
          //  print $line."\n";
            if(preg_match("/$ping_count packets transmitted/",$line)){

                $pieces = explode(', ',$line);
                $p      = explode(' ',$pieces[1]);
                if($p[0] > 0){
                    $state = '1';
                }
            }
        }

        //Add an entry to the state table
        if($last_state != $state){
            $d['NaState']['id']        = '';
            $d['NaState']['na_id']     = $id;
            $d['NaState']['state']     = $state;
            $this->NaState->save($d);
        }
        print $state."\n";
       // print_r($feedback);

    }

}

?>
