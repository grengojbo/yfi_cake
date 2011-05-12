<?php

class RestartCheckerShell extends Shell {

    //-----------Call this shell like so:---------------------------------
    //"/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake restart_checker";
    //---------------------------------------------------------------------

    //=====================================================================
    //This script is called by cron every x minute(s) It will check to see if there are new changes to the 
    //models defines in the $uses array ('Realm' and 'Na' for now)
    //If there are any since last check+$cool_off_time => restart the FreeRADIUS server
    //=====================================================================

    //--------------------------------------------------------
    //---PREDEFINED VARIABLES---------------------------------
    //--------------------------------------------------------
    //Models that we will be using
    var $uses           = array('Realm','Na','Check');   //Tables to check for recent changes
    var $cool_off_time;

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
        $this->cool_off_time    = Configure::read('freeradius.back_off_minutes');
        //----------------------------------------
    }
    //==============END OF SECTION =======================

    

    function main(){

        //Check if there is a last entry for 'radius_restart'
        $q_r =$this->Check->find('first',array('conditions' => array('Check.name' =>'radius_restart')));

        $id;
        if($q_r){

            $id         = $q_r['Check']['id'];
            $modified   = $q_r['Check']['modified'];
            print("FreeRADIUS Restarted on $modified\n");
            $this->_restart_freeradius($id,$modified);

        }else{

            print("FreeRADIUS Not Restarted Yet");
            $this->_restart_freeradius();
        }


    }

    function _restart_freeradius($id,$last_time = null){

        $restart_string = "sudo /etc/init.d/radiusd restart";

        if($last_time == null){
            //Restart FreeRADIUS (First Time)
            system($restart_string);
            //Add an entry to the checks table
            $d['Check']['id']       = '';
            $d['Check']['name']     = 'radius_restart';
            $d['Check']['value']    = '1';
            $this->Check->save($d);

        }else{

            //Realm Check
            $q_r = $this->Realm->find('first',array('order' => array('Realm.modified DESC')));
            $modified = $q_r['Realm']['modified'];
            print("Realm modified on $modified\n");
            if($this->_restart_check($last_time,$modified)){
                system($restart_string);
                //Update the Check table
                $this->Check->id = $id;
                $this->Check->saveField('value','1');
                return;     //We do not want to restart twice!
            }

            //Nas Check
            $q_r    = $this->Na->find('first',array('order' => array('Na.modified DESC')));
            $modified = $q_r['Na']['modified'];
            print("Nas modified on $modified\n");
            if($this->_restart_check($last_time,$modified)){
                system($restart_string);
                //Update the Check table
                $this->Check->id = $id;
                $this->Check->saveField('value','1');
                return;     //We do not want to restart twice!
            }
            //Do a dummy restart
             
            


        }
    }


    function _restart_check($last_time,$modified){

        $diff = (strtotime($last_time)-(strtotime($modified)));
        if($diff < 0){
                //We need to restart the server BUT CHECK if the last_time is past the cool_off period
            $last_plus_cool_off = strtotime($last_time)+ ($this->cool_off_time * 60);
            $dateTime       = new DateTime("now");
            $date_now       = $dateTime->format("Y-m-d H:i:s"); 
            $now            = strtotime($date_now);
            $clear          = false;

            $clear          = $now-$last_plus_cool_off;
            if($clear > 0){

                print ("We can restart!\n");
                return true;
            }

        }
        return false;
    }


}

?>