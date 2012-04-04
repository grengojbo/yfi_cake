<?php

class VoucherCleanupShell extends Shell {

    //-----------Call this shell like so:---------------------------------
    //"/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake voucher_cleanup -check_for_used "
    // Run it as www-data because of the temp files belonging to www-data
    //---------------------------------------------------------------------


    //==========================================================================
    //--We run this script at different intervals with different switches----
    //--The following switches exists for this shell:------------------------
    // -check_for_used      => This checks the accounting records starting form a last recorded ID until the last one and see if there is vouchers with the username, and updates the status
    // -check_for_depleted  => This queries the vouchers table and filter on 'used' status to see if the voucher is not already depleted by doing a radius auth query
    // STATUS: `status` enum('new','used','depleted','expired') default 'new'
    // (-check_for_used will run more reqularly than -check_for_depleted)
    //==========================================================================


    //--------------------------------------------------------
    //---PREDEFINED VARIABLES---------------------------------
    //--------------------------------------------------------
    //Models that we will be using
    var $uses       = array('Voucher','Radacct','Check');

    //For debug info
    var $debug_flag = true;
    var $radtest_script;
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

        $this->radtest_script = Configure::read('freeradius.radtest_script');
        //----------------------------------------
    }
    //==============END OF SECTION =======================

    

    function main(){

        if(array_key_exists('check_for_used',$this->params)){
            print("===Check For Newly Used Status===\n");

            $this->_check_for_used();
        }

        if(array_key_exists('check_for_depleted',$this->params)){
            print("===Check For Newly Depleted Status===\n");
            $this->_check_for_depleted();

        }
    }

    function _check_for_depleted(){

        $conditions = array(array('Voucher.status <>' => 'new'),array('Voucher.status <>' => 'depleted'));

        $q_r    = $this->Voucher->find('all',array('conditions' => $conditions));
        foreach($q_r as $item){
            $username   = $item['Radcheck']['username'];
            $password   = $item['Radcheck']['value'];
            $id         = $item['Voucher']['id'];
            $this->_check_voucher($username,$password,$id);
        }
    }


    function _check_for_used(){

        //Check if there is a 'radacct_last_id' entry in the check model
        $q_r = $this->Check->find('first',array('conditions' => array('Check.name' => 'radacct_last_id')));

        $first_time_flag = false;
        $id;
        $last_id;
        $conditions = array();
        if(!$q_r){
            print "First time\n";
            $first_time_flag = true;
            array_push($conditions,array("Radacct.radacctid >" => 0));
        }else{
            $id         = $q_r['Check']['id'];
            $last_id    = $q_r['Check']['value'];
            array_push($conditions,array("Radacct.radacctid >" => $last_id));
        }

        $q_r = $this->Radacct->find('all',array('conditions' => $conditions,'fields' => 'DISTINCT Radacct.username'));
        $last_username;
        if(count($q_r) == 0){
            print "No new entries.... \n";
            return;
        }

        foreach($q_r as $item){

            $last_username   = $item['Radacct']['username'];
            $this->_update_status_to_used($last_username);

        }


        //------------------------------------------------------------------
        //-------- Update the Checks table to include tha last ID checked---
        //------------------------------------------------------------------
        //Get the radacctid for the last username
        $q_r    = $this->Radacct->find('first',array('conditions' =>array('Radacct.username' => $last_username),'order' => array('Radacct.radacctid DESC')));
        $last_id = $q_r['Radacct']['radacctid'];
       // print("The Last ID is $last_id\n");

        //Update the Checks table
        if($first_time_flag){

            $d['Check']['id']       = '';
            $d['Check']['name']     = 'radacct_last_id';
            $d['Check']['value']    = $last_id;
            $this->Check->save($d);
            $this->Check->id        = false;

        }else{

            $this->Check->id = $id;
            $this->Check->saveField('value',$last_id);
        }
        //----------------------------------------------------------
    }


    function _check_voucher($username,$password,$id){

        print ("Testing the status for $username $password\n");
        $radscenario = $this->radtest_script;
        $output = array();
        exec("perl $radscenario $username $password",$output);
       // print_r($output);
        foreach($output as $line){
            $line = rtrim($line);
            $line = ltrim($line);

            if(preg_match('/^Received/',$line)){

                if(preg_match('/^Received Access-Reject/',$line)){
                    $this->_update_voucher_status($id,'depleted');  //Check only for expired ones
                }
            }
        }
    }

    function _update_voucher_status($voucher_id,$status){

        $this->Voucher->id = $voucher_id;
        $this->Voucher->saveField('status',$status);

    }

    function _update_status_to_used($username){

        //Check if there is a voucher for this username
        $q_r = $this->Voucher->find('first',array('conditions' => array('Radcheck.username'=>$username)));
        if($q_r){
            $this->Voucher->id = $q_r['Voucher']['id'];
            $this->Voucher->saveField('status','used');
        }
    }

}

?>