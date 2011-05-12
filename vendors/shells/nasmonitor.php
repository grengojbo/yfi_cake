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
        /*
        $this->path = Configure::read('Reprepro.base');
        $this->subs = array(
                        'confs' =>  Configure::read('Reprepro.confs'),
                        'dbs'   =>  Configure::read('Reprepro.dbs'),
                        'dists' =>  Configure::read('Reprepro.dists'),
                        'logs'  =>  Configure::read('Reprepro.logs')
                );
        $this->filters = 'filters';
        */
        //----------------------------------------
    }
    //==============END OF SECTION =======================

    

    function main(){

        //---Initialization code------------------------------
        App::import('Core', array('View', 'Controller'));
        App::import('Controller', array('Nas'));
        $this->NasController = new NasController();
        $this->NasController->constructClasses();

        $qr = $this->Na->find('all',array());
        foreach($qr as $item){
            //Check if we need to monitor this one
            if($item['Na']['monitor'] == 1){
                //Get the last state
                $last_state = 'new';
                if(count($item['NaState']) > 0){
                    $last_state = $item['NaState'][0]['state'];
                }
                $nasname    = $item['Na']['nasname'];
                $id         = $item['Na']['id'];
                $this->_test_device($last_state,$nasname,$id);
                
    
            }

        }
      //  print_r($qr);
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