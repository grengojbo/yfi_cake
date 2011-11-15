<?php

class CmpVoucherComponent extends Object {

    //----Component with common functions related to Vouchers----------------------------------------------
    //---NOTE: This idea came in late - so not all controllers use it, newer controllers will make use of it -----
    //---This was to allow easy third party integrations ---------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------

    //---The controller who uses the component needs to use the following models:
    //-> Radacct Radusergroup Radgroupcheck Radcheck

    var $components = array('Session','Rights','Formatter');

    function initialize(&$controller) {
        // saving the controller reference for later use
        $this->controller =& $controller;
    }


    function get_current_usage($username,$yfi_voucher){
        return $this->_get_usage($username,$yfi_voucher);
    }

    function add_voucher($voucher_info){

        //print_r($voucher_info);
        /*
        //----------- The $voucher_info array needs th have the following keys -----
        'user_id'       => The access provider's id under which this voucher will be created
        'precede'       => A precede string to the voucher
        'iso_format'   => The iso format of the expiry date when the voucher should expire
        'profile_name'  => The name of the profile a voucher belongs to
        'profile_id'    => The id of the profile a voucher belongs to
        'realm_name'    => The suffix of the @<realm_name>
        'realm_id'      => The id of the realm a voucer belongs to
        'yfi_voucher'   => The value of the voucher ( EG 1-00-00-00)
        //------------------------------------------------------------------------
        */

        //_________________________________________________________________________
        //--- 22-6-10-------
        $filename   = "/tmp/yfi_voucher_busy";
        $t          = time();
        $t_out      = $t + 10; //10 seconds should be enough

        if(file_exists($filename)){ //If file exists loop for a max of $t_out seconds checking if the file gets deleted
            while(file_exists($filename)&&($t_out > time())){
                sleep(1);
            }
        }else{  //File dos not exists, create it
            $handle = fopen("/tmp/yfi_voucher_busy", "w");
        }
        //-- END 22-6-10----
        //=========================================================================

        //---- Required Values ----------
        $precede    = $voucher_info['precede'];
        $realm_name = $voucher_info['realm_name'];
        $yfi_voucher= $voucher_info['yfi_voucher'];
        $iso_format = $voucher_info['iso_format'];
        $profile_name = $voucher_info['profile_name'];
        $user_id    = $voucher_info['user_id'];
        $profile_id = $voucher_info['profile_id'];
        $realm_id   = $voucher_info['realm_id'];


        $voucher_value      = $this->_detemine_voucher_name($precede,$realm_name);
        $voucher_password   = $this->_generatePassword();
        $radcheck_id        = $this->_add_entry('Radcheck',$voucher_value,'Cleartext-Password',$voucher_password);
        //Add the Yfi-Voucher attribute
        $this->_add_entry('Radcheck',$voucher_value,'Yfi-Voucher',$yfi_voucher);

        //Add the WISPr-Session-Terminate-Time entry
        $this->_add_entry('Radreply',$voucher_value,'WISPr-Session-Terminate-Time',$iso_format);
        //Add the profile (group)
        $this->_add_radusergroup($voucher_value,$profile_name);

        //Add the voucher to the voucher model
        $this->_add_single_voucher($radcheck_id,$user_id,$realm_id,$profile_id);
        $voucher_id = $this->controller->Voucher->id;

        $return_array = array();
        $return_array['username']   = $voucher_value;
        $return_array['password']   = $voucher_password;
        $return_array['voucher_id'] = $voucher_id;

        //___________________________________________________
        //--- 22-6-10-----------
        //remove file
        unlink($filename);
        //--- END 22-6-10 ------
        //==================================================


        return $return_array;
    }

    function _detemine_voucher_name($precede='',$realm){

        $realm          = '@'.$realm;
        //We sit with a genuine problem when a person DOES not specify a precede
        //Then we have to do a general search for ALL vouchers for the specified realm, and loop them to determine the LAST one
        if($precede == ''){
            $reply  =   $this->controller->Voucher->find('all',array(
                            'fields'        =>array('Radcheck.username'),
                            'conditions'    =>array('Radcheck.username LIKE' => '%'.$realm),
                            'order'         => array('Radcheck.username DESC'))
                        );

            $last_value = 0;
            foreach($reply as $result){
                //Check if if has a precede (IE contains a minus)
                $unm = $result['Radcheck']['username'];
                if(!preg_match("/-.+@$realm/",$unm)){
                    $val = preg_replace("/$$realm/",'',$unm);
                    if($val > $last_value){ //If bigger, make the new one the biggest
                        $last_value = $val;
                    }
                }
            }
            $next_number = sprintf("%05d", $last_value+1);
            return $next_number.$realm;
        }else{

            $precede        = $precede.'-';
            $reply          = $this->controller->Voucher->find('first',array(
                                                        'fields'=>array('Radcheck.username'),
                                                        'conditions'=>array('Radcheck.username LIKE' => $precede.'%'.$realm),
                                                        'order'=> array( 'Radcheck.username DESC'))
                                            );
            $last_entry     =($reply['Radcheck']['username']);
            $voucher_name;

            if(!$last_entry){
                $voucher_name = $precede."00001".$realm;
            }else{

                //Get the last number
                $number = preg_replace("/^$precede/",'',$last_entry);
                $number = preg_replace("/$$realm/",'',$number);
                $number = sprintf("%05d", $number+1);
                $voucher_name = $precede.$number.$realm;
            }
            return $voucher_name;
        }
    }

    function _generatePassword ($length = 8){

        // start with a blank password
        $password = "";
        // define possible characters
       // $possible = "!#$%^&*()+=?0123456789bBcCdDfFgGhHjJkmnNpPqQrRstTvwxyz";
        $possible = "0123456789bBcCdDfFgGhHjJkmnNpPqQrRstTvwxyz";
        // set up a counter
        $i = 0; 
        // add random characters to $password until $length is reached
        while ($i < $length) { 

            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
            // we don't want this character if it's already in the password
            if (!strstr($password, $char)) { 
                $password .= $char;
                $i++;
            }
        }
        // done!
        return $password;
    }

    function _add_entry($model,$username,$attribute,$value,$op = '=='){

        $count = 0;
        $new_id;
        //Check if the entry exists - we do not add double's
        if($model == 'Radcheck'){
            $count =$this->controller->Radcheck->find('count',array('conditions'=>array('Radcheck.username' => $username, 'Radcheck.attribute' => $attribute)));
        }
        if($model == 'Radreply'){
            $count =$this->controller->Radreply->find('count',array('conditions'=>array('Radreply.username' => $username, 'Radreply.attribute' => $attribute)));
        }
        if($count > 0){
            return;     //Entry already there we do not tolerate duplicates
        }

        $rc = array();
        $rc["$model"]['id']         = '';
        $rc["$model"]['username']   = $username;
        $rc["$model"]['attribute']  = $attribute;
        $rc["$model"]['op']         = $op;
        $rc["$model"]['value']      = $value;
        if($model == 'Radcheck'){
            $this->controller->Radcheck->save($rc);
            $new_id = $this->controller->Radcheck->id;
            $this->controller->Radcheck->id  =false;
        }
        if($model == 'Radreply'){
            $this->controller->Radreply->save($rc);
            $new_id = $this->controller->Radreply->id;
            $this->controller->Radreply->id  =false;
        }
        return $new_id;
    }

    function _add_radusergroup($username,$groupname){

        $this->controller->Radusergroup->id =false;
        $rc = array();
        $rc["Radusergroup"]['username']   = $username;
        $rc["Radusergroup"]['groupname']  = $groupname;
        $rc["Radusergroup"]['priority']   = '1';
        $this->controller->Radusergroup->save($rc);
    }

    function _add_single_voucher($radcheck_id,$user_id,$realm_id,$profile_id){

        $v['Voucher']['id']             = '';
        $v['Voucher']['radcheck_id']    = $radcheck_id;
        $v['Voucher']['user_id']        = $user_id;
        $v['Voucher']['realm_id']       = $realm_id;
        $v['Voucher']['profile_id']     = $profile_id;
        $this->controller->Voucher->save($v);
    }



    function _get_usage($username,$yfi_voucher){

        //----------------Get The Account Values------------------------------------
        $data_total = 0;
        $time_total = 0;
        $voucher_time_total;
        $first_login_time ='';
        $acct = $this->controller->Radacct->find('all',array('conditions' => array('Radacct.username'   => $username)));

        if(count($acct) > 0){
            $first_login_time   = $acct[0]['Radacct']['acctstarttime'];
        }

        foreach($acct as $item){
            $data_total         = $data_total+ $item['Radacct']['acctinputoctets']+$item['Radacct']['acctoutputoctets'];
            $time_total         = $time_total+ $item['Radacct']['acctsessiontime']; //(We do not use this time since we use Yfi-Voucher)
        }
        //-------------------------------------------------------------------------


        //--------------------------Get The Avialable (If applicable) Time & Data ------
        //Get the user's profile
        $q_r          = $this->controller->Radusergroup->find('first',array('conditions' => array('Radusergroup.username' => $username)));
        $profile      = $q_r['Radusergroup']['groupname'];

        //----DATA USED------------------------------------------------------------------------
        //Check if ChilliSpot-Max-All-Octets is defined - and how much?
        $q_r          = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'ChilliSpot-Max-All-Octets')));
        $chilli_data = 0;
        if($q_r  != ''){
            $chilli_data   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'ChilliSpot-Max-All-Octets')));
        if($q_r     != ''){
            $chilli_data   = $q_r['Radcheck']['value'];
        }
        //Add extra Caps if applicable
        if($chilli_data == 0){
            $chilli_data = 'NA';
        }else{
            $chilli_data   = $chilli_data - $data_total;
        }
        //--------------------------------------------------------------------------------------

        //----- TIME USED ----------------------------------------------------------------------
        $time_used = 0;
        if($first_login_time != ''){
            $time_used = strtotime("now") - strtotime($first_login_time);
        }

        //======= Time based Vouchers =================
        //We may have a time-based voucher (Attribute Max-All-Session)
        $voucher_time_cap = '';
        $q_r        = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Max-All-Session')));
        if($q_r  != ''){
            $voucher_time_cap   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Max-All-Session')));
        if($q_r     != ''){
            $voucher_time_cap   = $q_r['Radcheck']['value'];
        }
        if($voucher_time_cap != ''){
            $voucher_time_avail = $voucher_time_cap - $time_total ;
        }
        //====== END Time based Vouchers ==================

        //--------------------------------------------------------------------------------------

        //----- TIME AVAIALBLE ----------------------------------------------------------------
        $elements       = explode('-',$yfi_voucher);
        $day_seconds    = $elements[0] * 60 * 60 * 24;
        $hour_seconds   = $elements[1] * 60 * 60;
        $min_seconds    = $elements[2] * 60;
        $seconds        = $elements[3];
        $time_cap       = ($day_seconds + $hour_seconds + $min_seconds + $seconds);
        $time_avail     = $time_cap - $time_used;

        //======Time based voucher add on ======
        if($voucher_time_cap != ''){
            if($time_avail > $voucher_time_avail){      //If the time based voucher has less time than Yfi-Voucher attribute 
                $time_avail = $voucher_time_avail;
                $time_used  = $time_total;
            }
        }
        //===== END Time based voucher add on ======

        //------------------------------------------------------------------------------------
        $return_array = array(
                                'time_used'     => $time_used, 
                                'time_avail'    => $time_avail,
                                'data_used'     => $data_total,
                                'data_avail'    => $chilli_data,
                        );
        return $return_array;
    }
}

?>
