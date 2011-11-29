<?php
class RadacctsController extends AppController {

   // var $scaffold;
    var $helpers    = array('Html', 'Form','Javascript' );
    var $components = array('Session','Rights','Json','Dojolayout','Formatter','Kicker');    //Add the locker component
    var $uses       = array('Radacct','Voucher','Na','User','Device','Action');

    //------------------------------------------------------------------
    //--- List of rights that can be tweaked ---------------------------
    //------------------------------------------------------------------
    /*
        1.) json_show_active
        2.) json_kick_users_off
        3.) json_stop_open
    */
    //-----------------------------------------------------------------

    function json_show_active($quick=false){

        $this->layout = 'ajax';

        $auth_info = $this->Session->read('AuthInfo');
        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        $group = $auth_info['Group']['name'];

        if($group == 'Users'){  //They are not allowed to see anything

            $this->set('json_return',$this->Json->permAuthListEmpty());
            return;
        }

        //=====================================
        $conditions = array();
        if($group == 'Access Providers'){   //They can only see whet the are permitted to see
            //Access Providers should have a list of Realms
            //Check if there are realms assinged to this user and then build the query form it.
            if(!empty($auth_info['Realms'])){
                $realm_filter = array();
                foreach($auth_info['Realms'] as $realm_line){
                    $name_ends_with = $realm_line['append_string_to_user'];
                    array_push($realm_filter,array("Radacct.realm" => $name_ends_with));
                }
            }
            array_push($conditions,array('or' => $realm_filter));
        };
        //=======================================

        $cond_array = array('Radacct.acctstoptime' =>null);  #Use This one for FreeRADIUS version 2.X (Default value for stoptime changed)
        //$cond_array = array('Radacct.acctstoptime' =>"0000-00-00 00:00:00");
        array_push($cond_array,$conditions);

        if(array_key_exists('itemId',$this->params['url'])){
            $specific_id = $this->params['url']['itemId'];
            array_push($cond_array,array('Radacct.radacctid' =>$specific_id));
        }

        $active = $this->Radacct->find('all',array('conditions'=>$cond_array,'recursive' => 0));

        $json_return   = array();
        $json_return['label']      = 'username';
        $json_return['identifier'] = 'id';
        $json_return['items']      = array();

        foreach($active as $item){
            $id         = $item['Radacct']['radacctid'];
            if(!$quick){
                $username   = $item['Radacct']['username'];
                $realm      = $item['Radacct']['realm'];
                $voucher_id = $this->_voucher_id_find($username);
                $user_id    = $this->_user_id_find($username);
                $nasip      = $item['Radacct']['nasipaddress'];
            }
            $start      = $item['Radacct']['acctstarttime'];
            $io         = $item['Radacct']['acctinputoctets'];
            $oo         = $item['Radacct']['acctoutputoctets'];
            $ip         = $item['Radacct']['framedipaddress'];
            
            $start      = $this->_diff_in_time($start);
            //$to         = str_pad(($oo+$io),20, "0", STR_PAD_LEFT);
            //$io         = str_pad($io,20, "0", STR_PAD_LEFT);
            //$oo         = str_pad($oo,20, "0", STR_PAD_LEFT);
            $to         = $oo+$io;
            $io         = $io;
            $oo         = $oo;
            

            if($quick){
                array_push($json_return['items'],array('id' => $id,'connected'=> $start,'input_octets' =>$io, 'output_octets' => $oo,'total_octets' => $to));
            }else{
                array_push($json_return['items'],array(
                                                        'id'            => $id,
                                                        'voucher_id'    => $voucher_id,
                                                        'user_id'       => $user_id,
                                                        'realm'         => $realm,
                                                        'nas'           => $nasip,
                                                        'ip'            => $ip, 
                                                        'username'      => $username,
                                                        'connected'     => $start,
                                                        'input_octets'  => $io, 
                                                        'output_octets' => $oo,
                                                        'total_octets'  => $to
                                                ));
                
            }
        }
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }


     function json_stats(){

        $this->layout = 'ajax';

        $auth_info = $this->Session->read('AuthInfo');
        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        $group = $auth_info['Group']['name'];

        if($group == 'Users'){  //They are not allowed to see anything
            $this->set('json_return',$this->Json->permAuthListEmpty());
            return;
        }

        //=====================================
        $conditions = array();
        if($group == 'Access Providers'){   //They can only see whet the are permitted to see
            //Access Providers should have a list of Realms
            //Check if there are realms assinged to this user and then build the query form it.
            if(!empty($auth_info['Realms'])){
                $realm_filter = array();
                foreach($auth_info['Realms'] as $realm_line){
                    $name_ends_with = $realm_line['append_string_to_user'];
                    array_push($realm_filter,array("Radacct.realm" => $name_ends_with));
                }
            }
            array_push($conditions,array('or' => $realm_filter));
        };
        //=======================================

        $sort           = $this->_returnOrderClause();

        //----Lets get to the correct Page------
        $start =0;
        $count;

        if(array_key_exists('start',$this->params['url'])){
            $start = $this->params['url']['start'];
        }

        if(array_key_exists('count',$this->params['url'])){

            $count = $this->params['url']['count'];
        }

        if($start == 0){

            $page = 1;
        }else{

            $page = ($start/$count)+1; 
        }
        //-----END Page Check--------------


         //__________Dummy Dates___________________________
        //$start_date = '2009-10-01 00:00:00';
        //$end_date   = '2009-10-31 23:59:59';
        $start_date = date ("Y-m-d H:i:s", $this->params['url']['sd']);
        $end_date   = date ("Y-m-d H:i:s", ($this->params['url']['ed']+(60*60*24)-1)); //Go to the end of the day
        //________ END Dummy Dates _______________________

        $cond_array = array('Radacct.acctstarttime >=' =>$start_date, 'Radacct.acctstoptime <=' =>$end_date);  
         //$cond_array = array(); 
        array_push($cond_array,$conditions);
        array_push($cond_array,$this->_returnSearchFilterConditions());

        //----Get the count
        $list    = $this->Radacct->find('all',array(
                                                    'conditions' => $cond_array
                                                    )
                                                );

        //Required for the QueryDataStore (Dojo)
        $json_return   = array();
        $json_return['numRows']    = count($list);

        $active = $this->Radacct->find('all',array(
                                            'conditions'    =>  $cond_array,
                                            'recursive'     => 0,
                                            'limit'         => $count,
                                            'page'          => $page,
                                            'order'         => $sort
                                            ));

        
        $json_return['label']      = 'username';
        $json_return['identifier'] = 'id';
        $json_return['items']      = array();

        foreach($active as $item){
            $id         = $item['Radacct']['radacctid'];
            $username   = $item['Radacct']['username'];
            $realm      = $item['Radacct']['realm'];
            $time       = $item['Radacct']['acctsessiontime'];
            $io         = $item['Radacct']['acctinputoctets'];
            $oo         = $item['Radacct']['acctoutputoctets'];
            $ip         = $item['Radacct']['framedipaddress'];

            $to         = $oo+$io;
            $io         = $io;
            $oo         = $oo;
            array_push($json_return['items'],array(
                                                        'id'                => $id,
                                                        'realm'             => $realm,
                                                        'ip'                => $ip, 
                                                        'username'          => $username,
                                                        'acctstarttime'     => $item['Radacct']['acctstarttime'],
                                                        'acctstoptime'      => $item['Radacct']['acctstoptime'],
                                                        'acctsessiontime'   => $time,
                                                        'acctinputoctets'   => $io, 
                                                        'acctoutputoctets'  => $oo,
                                                        'total_octets'      => $to
                                                ));
        }
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }




    function json_kick_users_off(){

        $this->layout = 'ajax';

        $users_to_kick = array();

        foreach(array_keys($this->params['url']) as $key){
                if(preg_match('/^\d+/',$key)){
                    //----------------
                    $radacctid = $this->params['url'][$key];     //Add a template_realm for each realm selected
                    array_push($users_to_kick,$radacctid);
                    //-------------
                }
        }

        foreach($users_to_kick as $radacctid){

            $this->_kick_user($radacctid);
            usleep(5000);   //Rest half a second
            //Close the setup any way - perhaps it was an orphan session
            $now = date('Y-m-d h:i:s');
            $this->Radacct->id = $radacctid;
            $this->Radacct->saveField('acctstoptime', $now);
        }

        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }


    //-------------------------------------------------------
    //----- Special requests --------------------------------
    //----- Sometimes there are double entries in radaccts---
    //-- This removes all the doubles -----------------------
    //-------------------------------------------------------
    function duplicate_check(){

        $q_r = $this->Radacct->find('all',array( 

                    'conditions'    => array(),
                    'fields'        => array('Radacct.radacctid','Radacct.username','Radacct.acctsessionid', 'COUNT(Radacct.acctsessionid) AS count'),
                    'group'         => 'Radacct.acctsessionid HAVING (COUNT(Radacct.acctsessionid)>1)',
                    'order'         => 'Radacct.radacctid'
                ));

        foreach($q_r as $entry){
            $id = $entry['Radacct']['radacctid'];
            print "Deleting entry: $id\n";
            $this->Radacct->delete($id);
           // print_r($entry);
        }

    }
    //-------------------------------------------------------

    //*****************************************************************************************************
    //----------- Dynamic Actions -------------------------------------------------------------------------
    //*****************************************************************************************************
 
    function json_actions(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on templates tab----------------- 
        //--AP Sepcific: TODO --------------------------------------------------------
        //--Rights Completed:   TODO -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                     = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_activity();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }


    function _diff_in_time($date_start){

        $dateTime       = new DateTime("now");
        $date_now       = $dateTime->format("Y-m-d H:i:s");
        //Get the difference between it:
        $diff = abs(strtotime($date_now)-strtotime($date_start));
        return $this->_sec2hms($diff,true);

    }


    function _sec2hms ($sec, $padHours = false){

        // holds formatted string
        $hms = "";
    
        // there are 3600 seconds in an hour, so if we
        // divide total seconds by 3600 and throw away
        // the remainder, we've got the number of hours
        $hours = intval(intval($sec) / 3600); 

        // add to $hms, with a leading 0 if asked for
        $hms .= ($padHours) 
              ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
            : $hours. ':';
     
        // dividing the total seconds by 60 will give us
        // the number of minutes, but we're interested in 
        // minutes past the hour: to get that, we need to 
        // divide by 60 again and keep the remainder
        $minutes = intval(($sec / 60) % 60); 

        // then add to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

        // seconds are simple - just divide the total
        // seconds by 60 and keep the remainder
        $seconds = intval($sec % 60); 

        // add to $hms, again with a leading 0 if needed
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        // done!
        return $hms;
    }


    function _voucher_id_find($username){

        $count = $this->Voucher->find('count',array('conditions' => array('Radcheck.username' => $username)));
        if($count > 0){

            $qr = $this->Voucher->find('first',array('conditions' => array('Radcheck.username' => $username)));
            return $qr['Voucher']['id'];
        }
        return;
    }

    function _user_id_find($username){

        $count = $this->User->find('count',array('conditions' => array('Radcheck.username' => $username)));
        if($count > 0){

            $qr = $this->User->find('first',array('conditions' => array('Radcheck.username' => $username)));
            return $qr['User']['id'];
        }
        return;
    }

    function _kick_user($radacctid){
        //Determine the type of NAS device -
        $q_r = $this->Radacct->findByRadacctid($radacctid);
        //$username = $q_r['Radacct']['username'];
        //Get the IP of the NAS device
        //$nas_ip = $q_r['Radacct']['nasipaddress'];
        $this->Kicker->kick($q_r['Radacct']);
    }

    function _returnSearchFilterConditions(){

        //----------------Search Filter ----------------------
        $column;
        $condition;

        if(array_key_exists('username',$this->params['url'])){
            $column    = 'Radacct.username';
            $condition  = $this->params['url']['username'];
        }

        if(array_key_exists('realm',$this->params['url'])){
            $column    = 'Radacct.realm';
            $condition  = $this->params['url']['realm'];
        }

         //SQL-aaize it
        $condition  = preg_replace( '/\*/', '%', $condition);

        $conditions = array(); //This will grow in complexity

        array_push($conditions,array("$column LIKE" => "$condition")); //Add This AND filtertjie
        return $conditions;
    }



     function _returnOrderClause(){

         //-----------Order Clause---------------------------------------
        $s ='';
        $sord_order;
        if(array_key_exists('sort',$this->params['url'])){  //The sort option is not always present
            $sort       = $this->params['url']['sort'];
            //Check if it is ASCENDING or DESC
            if(preg_match('/^-.+/', $sort)){
                $sort_order = 'DESC';
            }else{
                $sort_order = 'ASC';
            }

            if(preg_match('/username/',$sort)){
                $s = "Radacct.username $sort_order";
            }

            if(preg_match('/realm/',$sort)){
                $s = "Radacct.realm $sort_order";
            }

            if(preg_match('/acctstarttime/',$sort)){
                $s = "Radacct.acctstarttime $sort_order";
            }

            if(preg_match('/acctstoptime/',$sort)){
                $s = "Radacct.acctstoptime $sort_order";
            }

            if(preg_match('/acctsessiontime/',$sort)){
                $s = "Radacct.acctsessiontime $sort_order";
            }

            if(preg_match('/acctinputoctets/',$sort)){
                $s = "Radacct.acctinputoctets $sort_order";
            }

            if(preg_match('/acctoutputoctets/',$sort)){
                $s = "Radacct.acctoutputoctets $sort_order";
            }
             if(preg_match('/total_octets/',$sort)){
                $s = array("Radacct.acctinputoctets $sort_order"); //This is a sort of jippo - since we can not order by the sum of two columns
            }

        }
        //-------END Order Clause---------------------------------------
        return $s;
    }

}
?>
