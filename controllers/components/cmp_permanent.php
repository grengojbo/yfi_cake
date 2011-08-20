<?php

class CmpPermanentComponent extends Object {

    //----Component with common functions related to Permanent Users----------------------------------------------
    //---NOTE: This idea came in late - so not all controllers use it, newer controllers will make use of it -----
    //---This was to allow easy third party integrations ---------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------

    //---The controller who uses the component needs to use the following models:
    //-> Radacct Extra Radusergroup Radgroupcheck Radcheck User and Credit

    var $components = array('Session','Rights','Formatter','Auth');

    function initialize(&$controller) {
        // saving the controller reference for later use
        $this->controller =& $controller;
    }


    function get_current_usage($username,$id){
        $now            = time();
        $date_to_start  = date("Y-m-d H:i:s",$now);        //Now
        $current_pair   = array();
        $current_pair   = $this->_return_start_and_end_date($date_to_start);
        return $this->_get_usage_during_span(1,$id,$username,$current_pair,false);    //We do not want to format the time here!

    }

    function get_usage_during_span($counter,$user_id,$username,$date_pair){

        return $this->_get_usage_during_span($counter,$user_id,$username,$date_pair);
    }

    function usage_prepaid($id){
        
        return $this->_usage_prepaid($id);
    }

    function add_permanent($permanent_info){

        $username       = $permanent_info['username'];
        $password       = $permanent_info['password'];
        $profile_name   = $permanent_info['profile_name'];
  

        $full_user  = $username.'@'.$permanent_info['realm_name'];

        $radcheck_id   = $this->_add_entry('Radcheck',$full_user,'Cleartext-Password',$password);

        //Add the profile (group)
        $this->_add_radusergroup($full_user,$profile_name);

        $d['User']['username']      = $full_user;
        $d['User']['password']      = $this->Auth->password($password);
        $d['User']['group_id']      = $this->_GroupUserId();
        $d['User']['radcheck_id']   = $radcheck_id;
        $d['User']['profile_id']    = $permanent_info['profile_id'];
        $d['User']['user_id']       = $permanent_info['user_id'];
        $d['User']['realm_id']      = $permanent_info['realm_id'];
        $d['User']['cap']           = $permanent_info['cap'];
        //User enabled by default
        $d['User']['active']        = 1; 

        //Optional fields
        $optional_fields = array("name","surname","email","phone");
        foreach($optional_fields as $field){
            if(array_key_exists($field,$permanent_info)){
                $d['User'][$field]      = $permanent_info[$field];
            }
        }


        $this->controller->User->save($d);
        $user_id = $this->controller->User->id;
    }


    function change_password($username,$password){

        $u  = $this->controller->User->find('count',array('conditions'=> array('User.username' => $username)));
        if($u > 0){

                //See what the id of this user is
                $q_r    = $this->controller->User->find('first',array('conditions' => array('User.username' => $username)));
                //Update the user with the new profile id
                $this->controller->User->id = $q_r['User']['id'];
                $this->controller->User->saveField('password', $this->Auth->password($password));
                //Update the radcheck table
                $q_r    = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Cleartext-Password'))); 

                $radcheck_id = $q_r['Radcheck']['id'];
                $this->controller->Radcheck->id = $radcheck_id;
                $this->controller->Radcheck->saveField('value',$password);
                return 0;
        }else{
                return 1;
        }

    }



    function _usage_prepaid($id,$single= null,$format_time=null){

        //This method will take the time when the user was created, and calculate the SUM of data as well as the SUM of time in radacct

        //Get the first entry for this user in radacct table - if empty we take today as reference and get the start and end of month for today
        $u  = $this->controller->User->find('first',array('conditions'=> array('User.id' => $id)));
        $username = $u['User']['username'];

        //We also take all the Internet Credits asigned to this user and sum it up. (along with any Yfi-Data / Yfi-Time values
        //Get the user's profile
        $q_r          = $this->controller->Radusergroup->find('first',array('conditions' => array('Radusergroup.username' => $username)));
        $profile      = $q_r['Radusergroup']['groupname'];

        //---Check if Yfi-Data is defined - and how much?----
        $q_r          = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Data')));
        $yfi_data = 'NA';
        if($q_r['Radgroupcheck']['value']  != ''){
            $yfi_data   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Data')));
        if($q_r['Radcheck']['value']     != ''){
            $yfi_data   = $q_r['Radcheck']['value'];
        }

        //---Check if Yfi-Time is defined - and how much?----
        $q_r          = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Time')));
        $yfi_time = 'NA';
        if($q_r['Radgroupcheck']['value']  != ''){
            $yfi_time   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Time')));
        if($q_r['Radcheck']['value']     != ''){
            $yfi_time   = $q_r['Radcheck']['value'];
        }

        //Get the sum of Internet Credits
        $q_r        = $this->controller->Credit->find('first', array('fields'=>array('SUM(Credit.data) AS data','SUM(Credit.time) AS time'),'conditions' => array('UsedBy.id' => $id)));

       // print_r($q_r);
        ($yfi_data == 'NA')||($yfi_data   = $yfi_data + $q_r[0]['data']);
        ($yfi_time == 'NA')||($yfi_time   = $yfi_time + $q_r[0]['time']);

        $q_r        = $this->controller->Radacct->find('first', array('fields'=>array('SUM(Radacct.acctinputoctets) AS input','SUM(Radacct.acctoutputoctets) AS output','SUM(Radacct.acctsessiontime) AS time'),'conditions' => array('Radacct.username' => $username)));

        //-----Total usage-----
        if($q_r[0]['input'] == ''){
            $total_in = 0;
        }else{
            $total_in = $q_r[0]['input'];
        }

        if($q_r[0]['output'] == ''){
            $total_out = 0;
        }else{
            $total_out = $q_r[0]['output'];
        }
        $total_data = $total_in + $total_out;

        if($q_r[0]['time'] == ''){
            $total_time = 0;
        }else{
            $total_time = $q_r[0]['time'];
        }

        //Check how much is available
        if($yfi_time == 'NA'){
            $time_avail = 'NA';
        }else{

            ($format_time)&&($time_avail = $this->Formatter->formatted_seconds($yfi_time - $total_time)); 
        }

        if($yfi_data == 'NA'){
            $data_avail = 'NA';
        }else{
            $data_avail = $yfi_data - $total_data;
        }

        ($format_time)&&($total_time =$this->Formatter->formatted_seconds($total_time));

        $item = array(
                                'id'            => 1,
                                'start'         => $u['User']['created'], 
                                'end'           => 'NA',
                                'extra_time'    => 'NA',
                                'extra_data'    => 'NA',
                                'time_used'     => $total_time, 
                                'time_avail'    => $time_avail,
                                'data_used'     => $total_data,
                                'data_avail'    => $data_avail,
                        );
        return $item;
    }


    function _get_usage_during_span($counter,$user_id,$username,$date_pair,$format_time=true){ //By default we format the time here!

        
        //Get the total time and data for this span
        
    
        //----------------Get The Account Values------------------------------------
        $data_total = 0;
        $time_total = 0;
        $acct = $this->controller->Radacct->find('all',array('conditions' => array(
                                                    'Radacct.acctstarttime <='      => $date_pair['end'],
                                                    array('or' => array('Radacct.acctstarttime >='      => $date_pair['start'],'Radacct.acctstoptime' => null)), //NOTE Verify this QUERY!
                                                    'Radacct.username'              => $username
                    )));

        foreach($acct as $item){
            $data_total     = $data_total+ $item['Radacct']['acctinputoctets']+$item['Radacct']['acctoutputoctets'];
            $time_total     = $time_total+ $item['Radacct']['acctsessiontime'];
        }
        //-------------------------------------------------------------------------

        //--------------------------Get the extra CAP Values------------------------
        $extra_time     = 0;
        $extra_data     = 0;
        $e =   $this->controller->Extra->find('all',array('conditions' => array(
                                                    'Extra.user_id'             => $user_id,
                                                    'Extra.created <='          => $date_pair['end'],
                                                    'Extra.created >='          => $date_pair['start'],
                    )));

        foreach($e as $item){

            if($item['Extra']['type'] == 'time'){
                $extra_time = $extra_time + $item['Extra']['value'];
            }

             if($item['Extra']['type'] == 'data'){
                $extra_data = $extra_data + $item['Extra']['value'];
            }

        }
        //--------------------------------------------------------------------------


        //--------------------------Get The Avialable (If applicable) Time & Data ------
        //Get the user's profile
        $q_r          = $this->controller->Radusergroup->find('first',array('conditions' => array('Radusergroup.username' => $username)));
        $profile      = $q_r['Radusergroup']['groupname'];

        //------------------------------------------------------------------------------------
        //Check if Yfi-Data is defined - and how much?
        $q_r          = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Data')));
        $yfi_data = 0;
        if($q_r  != ''){
            $yfi_data   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Data')));
        if($q_r     != ''){
            $yfi_data   = $q_r['Radcheck']['value'];
        }
        //Add extra Caps if applicable
        if($yfi_data == 0){
            $yfi_data = 'NA';
        }else{
            $yfi_data   = $yfi_data + $extra_data;
            $yfi_data   = $yfi_data - $data_total;
        }
        //--------------------------------------------------------------------------------------
        

         //------------------------------------------------------------------------------------
        //Check if Yfi-Time is defined - and how much?
        $q_r          = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Time')));
        $yfi_time = 0;
        if($q_r  != ''){
            $yfi_time   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Time')));
        if($q_r     != ''){
            $yfi_time   = $q_r['Radcheck']['value'];
        }
        //Add extra Caps if applicable
        if($yfi_time == 0){
            $yfi_time = 'NA';
        }else{
            $yfi_time   = $yfi_time + $extra_time;
            $yfi_time   = $yfi_time - $time_total;
            $yfi_time   = $yfi_time;
           // $yfi_time   = $this->Formatter->formatted_seconds($yfi_time);
        }
        //--------------------------------------------------------------------------------------

        ($format_time)&&($extra_time = $this->Formatter->formatted_seconds($extra_time));
        ($format_time)&&($time_total = $this->Formatter->formatted_seconds($time_total));
        $return_array = array(
                                'id'            => $counter,
                                'start'         => $date_pair['start'], 
                                'end'           => $date_pair['end'],
                                'extra_time'    => $extra_time,
                                'extra_data'    => $extra_data,
                                'time_used'     => $time_total,
                                'time_avail'    => $yfi_time,
                                'data_used'     => $data_total,
                                'data_avail'    => $yfi_data,
                        );
        return $return_array;
    }

    function return_start_and_end_date($date){

        return $this->_return_start_and_end_date($date);
    }

     function _return_start_and_end_date($date){

        //Get the date that we reset the usage for a month
        Configure::load('yfi');
        $reset_date = Configure::read('permanent_users.reset_day');
        $dates      = array();


        $unix_stamp = strtotime($date);     //Get the unix stamp for given date

        $l_assoc = localtime($unix_stamp, true);    //Get the components for this date
        //Start of month will be:
        if($l_assoc['tm_mday'] >= $reset_date){   
            $m = $l_assoc['tm_mon']+1;  //Use current month
        }else{
            $m = $l_assoc['tm_mon'];    //Use previous month
        }

        //mktime(hour,minute,second,month,day,year,is_dst) 
        $start_of_month = date("Y-m-d H:i:s",mktime(0,0,0,$m,$reset_date,($l_assoc['tm_year']+1900)));          //Start of month
        $end_of_month   = date("Y-m-d H:i:s",mktime(23,59,59,$m+1,$reset_date-1,($l_assoc['tm_year']+1900)));  //End of month

        $dates['start'] = $start_of_month;
        $dates['end']   = $end_of_month;

        return $dates;
    }


    function update_user_usage($user_id){

        $q_r        = $this->controller->User->findById($user_id);
        $username   = $q_r['User']['username'];
        $cap        = $q_r['User']['cap'];

        if($cap == 'prepaid'){
            $this->_update_prepaid_usage($user_id,$username);
        }else{
            $this->_update_user_usage($user_id,$username);
        }
    }


     function _update_user_usage($user_id,$username){

        Configure::load('yfi');
        $reset_date = Configure::read('permanent_users.reset_day');
        $dates      = array();

        $unix_stamp = strtotime("now");     //Get the unix stamp for given date

        $l_assoc = localtime($unix_stamp, true);    //Get the components for this date
        //Start of month will be:
        if($l_assoc['tm_mday'] >= $reset_date){   
            $m = $l_assoc['tm_mon']+1;  //Use current month
        }else{
            $m = $l_assoc['tm_mon'];    //Use previous month
        }

        //mktime(hour,minute,second,month,day,year,is_dst) 
        $date_end       = date("Y-m-d H:i:s",mktime(0,0,0,$m,$reset_date,($l_assoc['tm_year']+1900)));          //Start of month
        $date_start     = date("Y-m-d H:i:s",mktime(23,59,59,$m+1,$reset_date-1,($l_assoc['tm_year']+1900)));  //End of month

       //----------------Get The Account Values------------------------------------
        $data_total = 0;
        $time_total = 0;
        $acct = $this->controller->Radacct->find('all',array('conditions' => array(
                                                    'Radacct.acctstarttime <='      => $date_start,
                                                    'Radacct.acctstarttime >='      => $date_end, 
                                                    'Radacct.username'              => $username
                    )));

        foreach($acct as $item){
            $data_total     = $data_total+ $item['Radacct']['acctinputoctets']+$item['Radacct']['acctoutputoctets'];
            $time_total     = $time_total+ $item['Radacct']['acctsessiontime'];
        }
        //-------------------------------------------------------------------------

        //--------------------------Get the extra CAP Values------------------------
        $extra_time     = 0;
        $extra_data     = 0;
        $e =   $this->controller->Extra->find('all',array('conditions' => array(
                                                    'Extra.user_id'             => $user_id,
                                                    'Extra.created <='          => $date_start,
                                                    'Extra.created >='          => $date_end, 
                    )));

        foreach($e as $item){

            if($item['Extra']['type'] == 'time'){
                $extra_time = $extra_time + $item['Extra']['value'];
            }

             if($item['Extra']['type'] == 'data'){
                $extra_data = $extra_data + $item['Extra']['value'];
            }

        }
        //--------------------------------------------------------------------------


        //--------------------------Get The Avialable (If applicable) Time & Data ------
        //Get the user's profile
        $q_r          = $this->controller->Radusergroup->find('first',array('conditions' => array('Radusergroup.username' => $username)));
        $profile      = $q_r['Radusergroup']['groupname'];

        //------------------------------------------------------------------------------------
        //Check if Yfi-Data is defined - and how much?
        $q_r          = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Data')));
        $yfi_data = 0;
        if($q_r  != ''){
            $yfi_data   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Data')));
        if($q_r     != ''){
            $yfi_data   = $q_r['Radcheck']['value'];
        }
        //Add extra Caps if applicable
        if($yfi_data == 0){
            $yfi_data_usage = 'NA';
        }else{
            $yfi_data   = $yfi_data + $extra_data;
        }
        //--------------------------------------------------------------------------------------

        
        //------------------------------------------------------------------------------------
        //Check if Yfi-Time is defined - and how much?
        $q_r          = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Time')));
        $yfi_time = 0;
        if($q_r  != ''){
            $yfi_time   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Time')));
        if($q_r     != ''){
            $yfi_time   = $q_r['Radcheck']['value'];
        }
        //Add extra Caps if applicable
        if($yfi_time == 0){
            $yfi_time = 'NA';
        }else{
            $yfi_time   = $yfi_time + $extra_time;
        }
        //--------------------------------------------------------------------------------------

        //--Calculate the percentages----
        $used_data   = 'NA';
        if($yfi_data != 'NA'){
            $used_data = ($data_total / $yfi_data)*100;
            $used_data = sprintf("%.2f",$used_data);
        }

        $used_time = 'NA';
        if($yfi_time != 'NA'){
            $used_time = ($time_total / $yfi_time)*100;
            $used_time = sprintf("%.2f",$used_time);
        }

        //---Update---
        $d['User']['id']           = $user_id;
        $d['User']['data']         = $used_data;
        $d['User']['time']         = $used_time;
        $this->controller->User->save($d);
    }

    function _update_prepaid_usage($user_id, $username){

        
         //We also take all the Internet Credits asigned to this user and sum it up. (along with any Yfi-Data / Yfi-Time values
        //Get the user's profile
        $q_r          = $this->controller->Radusergroup->find('first',array('conditions' => array('Radusergroup.username' => $username)));
        $profile      = $q_r['Radusergroup']['groupname'];

        //========================================================
        //---Check if Yfi-Data is defined - and how much?----
        $q_r          = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Data')));
        $yfi_data = 'NA';
        if($q_r['Radgroupcheck']['value']  != ''){
            $yfi_data   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Data')));
        if($q_r['Radcheck']['value']     != ''){
            $yfi_data   = $q_r['Radcheck']['value'];
        }
        //---------------------------------------------------------

        //========================================================
        //---Check if Yfi-Time is defined - and how much?----
        $q_r          = $this->controller->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Time')));
        $yfi_time = 'NA';
        if($q_r['Radgroupcheck']['value']  != ''){
            $yfi_time   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->controller->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Time')));
        if($q_r['Radcheck']['value']     != ''){
            $yfi_time   = $q_r['Radcheck']['value'];
        }
        //---------------------------------------------------------

        //--Get the sum of Internet Credits--
        $q_r        = $this->controller->Credit->find('first', array('fields'=>array('SUM(Credit.data) AS data','SUM(Credit.time) AS time'),'conditions' => array('UsedBy.id' => $user_id)));

        //If the profile id data/time based record the total credits
        ($yfi_time == 'NA')||($yfi_time = $yfi_time + $q_r[0]['time']);
        ($yfi_data == 'NA')||($yfi_data = $yfi_data + $q_r[0]['data']);

        //===============================================================
        //Get the total usage
        $q_r        = $this->controller->Radacct->find('first', array('fields'=>array('SUM(Radacct.acctinputoctets) AS input','SUM(Radacct.acctoutputoctets) AS output','SUM(Radacct.acctsessiontime) AS time'),'conditions' => array('Radacct.username' => $username)));

        //-----Total usage-----
        if($q_r[0]['input'] == ''){
            $total_in = 0;
        }else{
            $total_in = $q_r[0]['input'];
        }

        if($q_r[0]['output'] == ''){
            $total_out = 0;
        }else{
            $total_out = $q_r[0]['output'];
        }
        $total_data = $total_in + $total_out;

        if($q_r[0]['time'] == ''){
            $total_time = 0;
        }else{
            $total_time = $q_r[0]['time'];
        }
        //------------------------------------------------------------------


        //--Calculate the percentages----
        //(used) / (avail)
        if($yfi_data != 'NA'){
            $yfi_data = ($total_data / $yfi_data)*100;
            $yfi_data = sprintf("%.2f",$yfi_data);
        }

        if($yfi_time != 'NA'){
            $yfi_time = ($total_time / $yfi_time)*100;
            $yfi_time = sprintf("%.2f",$yfi_time);
        }
        //---Update---
        $d['User']['id']           = $user_id;
        $d['User']['data']         = $yfi_data;
        $d['User']['time']         = $yfi_time;
        $this->controller->User->save($d);
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

    function _GroupUserId(){
        Configure::load('yfi');
        $user_name = Configure::read('group.user');
        $q_r    = $this->controller->Group->findByName($user_name);
        $groupId = $q_r['Group']['id'];
        return $groupId;
    }


}

?>
