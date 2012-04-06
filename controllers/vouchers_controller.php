<?
class VouchersController extends AppController {
    var $name       = 'Vouchers';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component
    var $uses       = array('Voucher','Realm','Radcheck','Radreply','Radusergroup','Batch','Profile','Radgroupcheck','Radgroupreply','Radacct','BatchesVoucher');

    //var $scaffold;


    //------------------------------------------------------------------
    //--- List of rights that can be tweaked ---------------------------
    //------------------------------------------------------------------
    /*
        1.) csv
        2.) pdf
        3.) json_index
        4.) json_add
        5.) json_add_batch
        6.) json_del
        7.) json_change_profile
        8.) json_add_private
        9.) json_del_private
       10.) json_edit_private
    */
    //-----------------------------------------------------------------


    function json_test_auth($voucher_id){

        $this->layout = 'ajax';

        $qr = $this->Voucher->findById($voucher_id);
        $username   = $qr['Radcheck']['username'];
        $password   = $qr['Radcheck']['value'];

        $output = array();

        Configure::load('yfi');
        $radscenario       = Configure::read('freeradius.radtest_script');

        exec("perl $radscenario $username $password",$output);

        $json_return['test']['status']  = 'warning';
        $json_return['test']['items']   = array();
        $json_return['test']['username']= $username;
        $json_return['test']['password']= $password;
        $answer_record = false;

        foreach($output as $line){

            $line = rtrim($line);
            $line = ltrim($line);

            if($answer_record == true){
                if(preg_match('/=/',$line)){
                    $pieces = explode('=',$line);
                    array_push($json_return['test']['items'], array('attr' => $pieces[0],'val' => $pieces[1]));
                }
            }

            if(preg_match('/^Received/',$line)){

                if(preg_match('/^Received Access-Accept/',$line)){
                    $json_return['test']['status']  = 'message';
                }
                $answer_record = true;
            }
        }
        $json_return['json']['status']  = 'ok';
        $this->set('json_return',$json_return);
    }


    function json_pdf_format_list(){

        $this->layout = 'ajax';
        $json_return['label']      = 'name';
        $json_return['identifier'] = 'id';
        

        $format_types = array(

                array('id'  =>  'generic',      'name'  => 'Generic A4'),
                array('id'  =>  'generic_ppv',  'name'  => 'Generic A4 Page/Voucher'),
                array('id'  =>  '5160',         'name'  => 'Avery 5160'),
                array('id'  =>  '5161',         'name'  => 'Avery 5161'),
                array('id'  =>  '5162',         'name'  => 'Avery 5162'),
                array('id'  =>  '5163',         'name'  => 'Avery 5163'),
                array('id'  =>  '5164',         'name'  => 'Avery 5164'),
                array('id'  =>  '5881',         'name'  => 'Avery 5881'),
                array('id'  =>  '6082',         'name'  => 'Avery 6082'),
                array('id'  =>  '6083',         'name'  => 'Avery 6083'),
               // array('id'  =>  '8600',         'name'  => 'Avery 8600'),
                array('id'  =>  'L7163',        'name'  => 'Avery L7163')
        );

        $json_return['items']      = $format_types;
        $json_return['json']['status']  = 'ok';
        $this->set('json_return',$json_return);
    }


    function csv(){

        $this->layout = 'csv';

        $voucher_list = array();
        foreach(array_keys($this->params['url'])as $key){

            if(preg_match('/^\d/',$key)){
               array_push($voucher_list,$this->params['url'][$key]);
            }
        }

        $voucher_detail = array();
        $counter = 0;
        foreach($voucher_list as $id){

            $qr = $this->Voucher->findById($id);
           // print_r($qr);

            $username       = $qr['Radcheck']['username'];
            $profile        = $qr['Profile']['name'];
            $realm          = $qr['Realm']['name'];
            $voucher_detail[$counter]               = $this->_get_voucher_detail($username);
            $voucher_detail[$counter]['realm']      = $realm;
            $voucher_detail[$counter]['profile']    = $profile;
            $voucher_detail[$counter]['username']   = $username;
            $voucher_detail[$counter]['created']    = $qr['Voucher']['created'];
            $voucher_detail[$counter]['status']     = $qr['Voucher']['status'];
            if($qr['Voucher']['status'] == 'new'){
                $voucher_detail[$counter]['first_used'] = 'NA';
                $voucher_detail[$counter]['data_used']  = 'NA';
            }else{
                //Get the sum of the data
                $data = $this->Radacct->find('first', array( 'fields' => array('SUM(acctinputoctets)+SUM(acctoutputoctets) AS data_usage'), 'conditions' => array('username' => $username)));
                $voucher_detail[$counter]['data_used']  = $data[0]['data_usage'];
                //Get the first time they logged in
                $data = $this->Radacct->find('first', array( 'fields' => array('Radacct.acctstarttime'), 'conditions' => array('Radacct.username' => $username), 'order'=> array('Radacct.acctstarttime ASC')));
                $voucher_detail[$counter]['first_used'] = $data['Radacct']['acctstarttime'];  
            }

            $counter++;
        }

        $this->set('csv_structure',$voucher_detail);
    
    }


    function pdf(){

        //___Change the language___
        Configure::load('yfi');
        $locale_location       = Configure::read('locale.location');
        $q_r  = $this->Language->findById($this->params['url']["language"]);
        $iso_name = $q_r['Language']['iso_name'];
        putenv("LANG=$iso_name.utf8"); 
        putenv("LANGUAGE=$iso_name.utf8"); 
        //setlocale(LC_ALL, "$iso_name.utf8");
        setlocale(LC_MESSAGES, "$iso_name.utf8");
        setlocale(LC_NUMERIC, "en");
        // Set the text domain as 'messages'
        $domain = 'messages';
        bindtextdomain($domain,$locale_location); 
        textdomain($domain);
        //_______________________________

        $this->layout = 'pdf';

        $format  = $this->params['url']["pdf_format"];
        $this->set('format',$format);
        $this->set('language',$iso_name);

        //Get the list of selected voucher id's
        $voucher_list = array();
        foreach(array_keys($this->params['url'])as $key){

            if(preg_match('/^\d/',$key)){
               array_push($voucher_list,$this->params['url'][$key]);
            }
        }

        if(preg_match('/generic/',$format)){
            $generic_structure = $this->_get_generic_pdf_data($voucher_list);
            $this->set('pdf_structure',$generic_structure);

        }else{

            $voucher_detail = array();
            $counter = 0;
            foreach($voucher_list as $id){

                $qr = $this->Voucher->findById($id);
                $username       = $qr['Radcheck']['username'];
                $profile        = $qr['Profile']['name'];
                $icon           = $qr['Realm']['icon_file_name'];
                $voucher_detail[$counter]   = $this->_get_voucher_detail($username);

                $voucher_detail[$counter]['icon']      = $icon;
                $voucher_detail[$counter]['profile']   = $profile;
                $voucher_detail[$counter]['username']  = $username;
               // $voucher_detail[$counter]['password']  = $voucher_info;

                $counter++;
            }

            $this->set('pdf_structure',$voucher_detail);
        }

    }

     function json_index(){

        $this->layout = 'ajax';
        #---------------------------------------------------------------------------------------------------------
        #--------This is one of the most complex methods of the controller. It has to take quite a lot in account-
        #---------------------------------------------------------------------------------------------------------

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        //--A Typical request will look like this:-------------------------------------------------------
        //--http://127.0.0.1/cake/yfi/vouchers/json_index?username=a*&start=80&count=40&sort=username----
        //-----------------------------------------------------------------------------------------------

        $conditions     = $this->_returnSearchFilterConditions();
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

       $json_return   = array();
        $json_return['label']      = 'username';
        $json_return['identifier'] = 'id';
        $json_return['items']      = array();

        $list   = $this->Voucher->find('all',array(
                                                    'conditions' => $conditions
                                                    )
                                                );

        //Required for the QueryDataStore (Dojo)
        $json_return['numRows']    = count($list);

        //Now we filted only the required page
        if(($start != '')&($count != '')){

             $list   = $this->Voucher->find('all',array(
                                                    'conditions'    => $conditions,
                                                    'limit'         => $count,
                                                    'page'          => $page,
                                                    'order'         => $sort 
                                                    )
                                                );

        }

        foreach($list as $item){
            $creator    = $item['User']['username'];
            $username   = $item['Radcheck']['username'];
            $password   = $item['Radcheck']['value'];
            $profile    = $item['Profile']['name'];
            $id         = $item['Voucher']['id'];
            $created    = $item['Voucher']['created'];
            $status     = $item['Voucher']['status'];
            $realm      = preg_replace( '/^.+@/', '', $username);
            $realm_q    = $this->Realm->find('first',array('conditions' => array('Realm.append_string_to_user' => $realm)));
            $realm_name = $realm_q['Realm']['name'];

            array_push($json_return['items'],array('id'=> $id,'profile'=> $profile, 'creator'=>$creator,'username' => $username, 'password' => $password,'realm' => $realm_name,'status' => $status,'created'=>$created));

        }

        $json_return['json']['status']  = 'ok';
        $this->set('json_return',$json_return);
    }


    function json_add(){

        $this->layout   = 'ajax';
        //----------------------------------
        //--- Check the rights -------------
        //----------------------------------

        $voucher_days   = $this->params['form']['valid_for'];
        $realm_id       = $this->params['form']['realm'];
        $profile_id     = $this->params['form']['profile'];
        $precede        = $this->params['form']['precede'];
        $realm          = $this->Realm->findById($realm_id);
        $realm_name     = $realm['Realm']['append_string_to_user'];
        $realm_name_full= $realm['Realm']['name'];
        $profile        = $this->Profile->findById($profile_id);
        $profile_name   = $profile['Profile']['name'];

        //Get the expire date in the right format (if specified)

        if(array_key_exists('expire_on',$this->params['form'])){
            $expire        = $this->params['form']['expire_on'];
            $timestamp     = strtotime($expire);
            //$iso_format    = date('c',$timestamp);
	    //--Remove timezone--
	    $iso_format    = date('o-m-d',$timestamp)."T".date('H:i:s',$timestamp);

        }

        $voucher_value  = $this->_detemine_voucher_name($precede,$realm_name);

        //Make sure this password is unique among all
        //Prime the values
        $voucher_password = $this->_generatePassword();
        $dup_count = $this->Radcheck->find('count',array('conditions' => array('attribute'=> 'Cleartext-Password', 'value' => $voucher_password)));
        while($dup_count > 0){  //Repeat until unique value is found
            $voucher_password = $this->_generatePassword();
            $dup_count = $this->Radcheck->find('count',array('conditions' => array('attribute'=> 'Cleartext-Password', 'value' => $voucher_password)));
        }


        $radcheck_id   = $this->_add_entry('Radcheck',$voucher_value,'Cleartext-Password',$voucher_password);
        //Add the Yfi-Voucher attribute
        $this->_add_entry('Radcheck',$voucher_value,'Yfi-Voucher',$voucher_days."-00-00-00");

        //Add the WISPr-Session-Terminate-Time entry
        if(array_key_exists('expire_on',$this->params['form'])){
            $this->_add_entry('Radreply',$voucher_value,'WISPr-Session-Terminate-Time',$iso_format);
        }
        //Add the profile (group)
        $this->_add_radusergroup($voucher_value,$profile_name);

        //Add the voucher to the voucher model
        $user_id = $this->Auth->user('id');
        $this->_add_voucher( $radcheck_id,$user_id);
        $voucher_id = $this->Voucher->id;

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }


    function json_add_batch(){

        $this->layout   = 'ajax';
        //----------------------------------
        //--- Check the rights -------------
        //----------------------------------


        $batch_name     = $this->params['form']['name'];
        $batch_size     = $this->params['form']['size'];
        $voucher_days   = $this->params['form']['valid_for'];
        $realm_id       = $this->params['form']['realm'];
        $profile_id     = $this->params['form']['profile'];
        $precede        = $this->params['form']['precede'];
        $realm          = $this->Realm->findById($realm_id);
        $realm_name     = $realm['Realm']['append_string_to_user'];
        $realm_name_full= $realm['Realm']['name'];
        $profile        = $this->Profile->findById($profile_id);
        $profile_name   = $profile['Profile']['name'];

        //Get the expire date in the right format
        if(array_key_exists('expire_on',$this->params['form'])){
            $expire        = $this->params['form']['expire_on'];
            $timestamp     = strtotime($expire);
	    //$iso_format    = date('c',$timestamp);
            //--Remove timezone--
            $iso_format    = date('o-m-d',$timestamp)."T".date('H:i:s',$timestamp);

        }

        //Add a batch 
        $batch_id = $this->_add_batch($batch_name,$realm_id);

        //Loop - creating vouchers -
        for ($i=1; $i<=$batch_size; $i++){

            $voucher_value  = $this->_detemine_voucher_name($precede,$realm_name);

            //Make sure this password is unique among all
            //Prime the values
            $voucher_password = $this->_generatePassword();
            $dup_count = $this->Radcheck->find('count',array('conditions' => array('attribute'=> 'Cleartext-Password', 'value' => $voucher_password)));
            while($dup_count > 0){  //Repeat until unique value is found
                $voucher_password = $this->_generatePassword();
                $dup_count = $this->Radcheck->find('count',array('conditions' => array('attribute'=> 'Cleartext-Password', 'value' => $voucher_password)));
            }


            $radcheck_id = $this->_add_entry('Radcheck',$voucher_value,'Cleartext-Password',$voucher_password);
            //Add the Yfi-Voucher attribute
            $this->_add_entry('Radcheck',$voucher_value,'Yfi-Voucher',$voucher_days."-00-00-00");

            //Add the WISPr-Session-Terminate-Time entry
            if(array_key_exists('expire_on',$this->params['form'])){
                $this->_add_entry('Radreply',$voucher_value,'WISPr-Session-Terminate-Time',$iso_format);
            }
            //Add the profile (group)
            $this->_add_radusergroup($voucher_value,$profile_name);

            //Add the voucher to the voucher model
            $user_id = $this->Auth->user('id');
            $this->_add_voucher( $radcheck_id,$user_id,$batch_id);
            $voucher_id = $this->Voucher->id;

            //Clear the ID's in a loop
            $this->Voucher->id  =false;
            $this->Radcheck->id =false;
            $this->Radreply->id =false;

        }

        //Get the feedback for the newly added batch
        $qr = $this->Batch->findById($batch_id);
        


        $json_return['json']['status']      = 'ok';

        $json_return['batch']['id']         = $batch_id;
        $json_return['batch']['name']       = $qr['Batch']['name'];
        $json_return['batch']['realm']      = $qr['Realm']['name']; 
        $json_return['batch']['size']       = count($qr['Voucher']); 
        $json_return['batch']['created']    = $qr['Batch']['created']; 

        $this->set('json_return',$json_return);

    }


    function json_del(){

        $this->layout   = 'ajax';
        //----------------------------------
        //--- Check the rights -------------
        //----------------------------------

        foreach(array_keys($this->params['url'])as $key){

            if(preg_match('/^\d/',$key)){
                $id = $this->params['url']["$key"];
                $this->BatchesVoucher->deleteAll( array("voucher_id" =>$id),false);
                $v              = $this->Voucher->find('first',array('conditions'=> array('Voucher.id' => $id)));
                $voucher_name   = $v['Radcheck']['username'];
                $this->Voucher->del($id,true);
                $this->Radcheck->deleteAll(     array("username"=>"$voucher_name"),false);
                $this->Radreply->deleteAll(     array("username"=>"$voucher_name"),true);
                $this->Radacct->deleteAll(      array("username"=>"$voucher_name"),true);
                
                $this->Radusergroup->removeUser($voucher_name);
            }
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


    function json_change_profile(){

        $this->layout   = 'ajax';

        $voucher_id  = $this->params['form']['id'];
        $profile_id  = $this->params['form']['profile'];

        $qr = $this->Voucher->findById($voucher_id);
        //Remove the old profile binding
        $voucher_name       = $qr['Radcheck']['username'];
        $this->Radusergroup->removeUser($voucher_name);

        //Add the new profile binding
        $profile        = $this->Profile->findById($profile_id);
        $profile_name   = $profile['Profile']['name'];
        $this->_add_radusergroup($voucher_name,$profile_name);

        //Update the voucher with the new profile id
        $this->Voucher->id = $voucher_id;
        $this->Voucher->saveField('profile_id', $profile_id);


        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_private_attributes($id){

        $this->layout   = 'ajax';

        $qr = $this->Voucher->findById($id);
        $voucher_name = $qr['Radcheck']['username'];

        $json_return['label']           = 'name';
        $json_return['identifier']      = 'id';
        $json_return['items']           = array();

        $qr = $this->Radcheck->findAllByUsername($voucher_name);
        foreach($qr as $item){

            $id = 'check_'.$item['Radcheck']['id'];
            array_push($json_return['items'], array('id'=>$id,'name' => $item['Radcheck']['attribute'],'type' =>'Check','op' => $item['Radcheck']['op'],'value' => $item['Radcheck']['value']));
        }

        $qr = $this->Radreply->findAllByUsername($voucher_name);
        foreach($qr as $item){

            $id = 'reply_'.$item['Radreply']['id'];
            array_push($json_return['items'], array('id'=>$id,'name' => $item['Radreply']['attribute'],'type' =>'Reply','op' => $item['Radreply']['op'],'value' => $item['Radreply']['value']));
        }

        $json_return['json']['status']  = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_add_private($id,$attribute){

        $this->layout   = 'ajax';

        $qr = $this->Voucher->findById($id);
        $voucher_name = $qr['Radcheck']['username'];

        $check_reply    = $this->params['form']['check_reply'];
        $op             = $this->params['form']['op'];
        $value          = $this->params['form']['value'];

        $this->_add_entry($check_reply,$voucher_name,$attribute,$value,$op);
       
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

     function json_del_private(){

        $this->layout = 'ajax';

         //--------Check the rights------
        /*
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        */
        //------------------------------------

        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $item_id = $this->params['url'][$key];
                $pieces = explode('_',$item_id);

                if($pieces[0] == 'check'){
                    $this->Radcheck->del($pieces[1], true);
                }

                if($pieces[0] == 'reply'){
                    $this->Radreply->del($pieces[1], true);
                }
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
 
    }


     function json_edit_private(){

        $this->layout = 'ajax';

         //--------Check the rights------
        /*
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        */
        //------------------------------------
        $id             = $this->params['form']['id'];
        $op             = $this->params['form']['op'];
        $value          = $this->params['form']['value'];
        $pieces = explode('_',$id);

        if($pieces[0] == 'check'){
            $d['Radcheck']['id']    = $pieces[1];
            $d['Radcheck']['op']    = $op;
            $d['Radcheck']['value'] = $value;
            $this->Radcheck->save($d);
        }
        if($pieces[0] == 'reply'){
            $d['Radreply']['id']    = $pieces[1];
            $d['Radreply']['op']    = $op;
            $d['Radreply']['value'] = $value;
            $this->Radreply->save($d);
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
 
    }


    function json_view_activity($id){

        $this->layout = 'ajax';

        $qr         = $this->Voucher->findById($id);
        $username   = $qr['Radcheck']['username'];

        $qr = $this->Radacct->find('all',array('conditions'=> array('Radacct.username' => $username),'order' => array('Radacct.acctstarttime DESC')));

       // print_r($qr);

        $json_return   = array();
        $json_return['label']      = 'username';
        $json_return['identifier'] = 'id';
        $json_return['items']      = array();

        foreach($qr as $item){

            $id             = $item['Radacct']['radacctid'];
            $username       = $item['Radacct']['username'];
            $realm          = $item['Radacct']['realm'];
            $start_time     = $item['Radacct']['acctstarttime'];
            $stop_time      = $item['Radacct']['acctstoptime'];
            $nas_mac        = $item['Radacct']['calledstationid'];
            $client_mac     = $item['Radacct']['callingstationid'];
            $client_ip      = $item['Radacct']['framedipaddress'];
            $bytes_tx       = $item['Radacct']['acctinputoctets'];
            $bytes_rx       = $item['Radacct']['acctoutputoctets'];

            $b_tx         = str_pad($bytes_tx,20, "0", STR_PAD_LEFT);
            $b_rx         = str_pad($bytes_rx,20, "0", STR_PAD_LEFT);
            $b_total      = str_pad(($bytes_rx+$bytes_tx),20, "0", STR_PAD_LEFT);
            $duration     = $this->_diff_in_time($start_time,$stop_time); 

            array_push($json_return['items'], array('id' => $id,'mac'=>$client_mac,'ip' => $client_ip,'start_time' => $start_time, 'stop_time' => $stop_time,'duration' => $duration,'bytes_tx' => $b_tx, 'bytes_rx' => $b_rx, 'bytes_total' => $b_total));

        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_del_activity(){

        $this->layout   = 'ajax';
        //----------------------------------
        //--- Check the rights -------------
        //----------------------------------

        foreach(array_keys($this->params['url'])as $key){

            if(preg_match('/^\d/',$key)){
                $id = $this->params['url']["$key"];
                $this->Radacct->del($id,true); 
            }
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }


    //*****************************************************************************************************
    //----------- Dynamic Actions -------------------------------------------------------------------------
    //*****************************************************************************************************

    function json_actions(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on profiles tab------------------ 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';

        $json_return['items']             = $this->Dojolayout->actions_for_vouchers();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_actions_view(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a profile view tab------------ 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_voucher_view();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_actions_for_voucher_profile(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a profile view tab (profile sub tab) 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_voucher_profile();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_actions_for_voucher_private(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a profile view tab (private sub tab) 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_voucher_private();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);


    }

     function json_actions_for_voucher_activity(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a profile view tab (private sub tab) 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_voucher_activity();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    //========================================================================================================================
    //===================== Private Functions ================================================================================
    //========================================================================================================================

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
            $count =$this->Radcheck->find('count',array('conditions'=>array('Radcheck.username' => $username, 'Radcheck.attribute' => $attribute)));
        }
        if($model == 'Radreply'){
            $count =$this->Radreply->find('count',array('conditions'=>array('Radreply.username' => $username, 'Radreply.attribute' => $attribute)));
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
            $this->Radcheck->save($rc);
            $new_id = $this->Radcheck->id;
            $this->Radcheck->id  =false;
        }
        if($model == 'Radreply'){
            $this->Radreply->save($rc);
            $new_id = $this->Radreply->id;
            $this->Radreply->id  =false;
        }
        return $new_id;
    }

    function _detemine_voucher_name($precede='',$realm){

        $realm          = '@'.$realm;
        //We sit with a genuine problem when a person DOES not specify a precede
        //Then we have to do a general search for ALL vouchers for the specified realm, and loop them to determine the LAST one
        if($precede == ''){
            $reply  =   $this->Voucher->find('all',array(
                            'fields'        =>array('Radcheck.username'),
                            'conditions'    =>array('Radcheck.username LIKE' => '%'.$realm),
                            'order'         => array( 'Radcheck.username DESC'))
                        );

            $last_value = 0;
            foreach($reply as $result){
                //Check if if has a precede (IE contains a minus)
                $unm = $result['Radcheck']['username'];
                if(preg_match("/-\d{5}$realm/",$unm)== 0){
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
            $reply          = $this->Voucher->find('first',array(
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

     function _add_radusergroup($username,$groupname){

        $this->Radusergroup->id =false;
        $rc = array();
        $rc["Radusergroup"]['username']   = $username;
        $rc["Radusergroup"]['groupname']  = $groupname;
        $rc["Radusergroup"]['priority']   = '1';
        $this->Radusergroup->save($rc);
    }

     function _add_voucher($radcheck_id,$user_id,$batch_id=''){

        //Add a binding to batch only for this.
        $this->Voucher->bindModel(
                array('hasAndBelongsToMany' => array(
                            'Batch' => array(
                                            'className' => 'Batch',  
                                            'joinTable' => 'batches_vouchers',  
                                            'foreignKey' => 'voucher_id',  
                                            'associationForeignKey' => 'batch_id',
                                            'fields' => array('Batch.id','Batch.name'), 
                                            'unique' => true 
                                        )
                            )
                    )
        );


        $v['Voucher']['id']             = '';
        $v['Voucher']['radcheck_id']    = $radcheck_id;
        $v['Voucher']['user_id']        = $user_id;
        $v['Voucher']['realm_id']       = $this->params['form']['realm'];
        $v['Voucher']['profile_id']     = $this->params['form']['profile'];
        if($batch_id != ''){    //If it is part of a batch add the batch ID

            $v['Batch']['Batch']            = array($batch_id);
        }
        $this->Voucher->save($v);

       $this->Voucher->unbindModel(array('hasAndBelongsToMany' => array('Batch')));
    }

    function _returnSearchFilterConditions(){

        //----------------Search Filter ----------------------
        $column;
        $condition;

        if(array_key_exists('username',$this->params['url'])){
            $column    = 'Radcheck.username';
            $condition  = $this->params['url']['username'];
        }

        if(array_key_exists('password',$this->params['url'])){
            $column    = 'Radcheck.value';
            $condition  = $this->params['url']['password'];
        }

        if(array_key_exists('profile',$this->params['url'])){
            $column    = 'Profile.name';
            $condition  = $this->params['url']['profile'];
        }

        if(array_key_exists('creator',$this->params['url'])){
            $column    = 'User.username';
            $condition  = $this->params['url']['creator'];
        }

        #HEADS UP - We need to re-design this thing!
        if(array_key_exists('realm',$this->params['url'])){
            $column    = 'Realm.name';
            $condition  = $this->params['url']['realm'];
        }

        $conditions = array(); //This will grow in complexity

        //Quick time filtertjie
        if(array_key_exists('time',$this->params['url'])){
            $time = $this->params['url']['time'];
            $time_stamp = false;
            if($time == 'past_hour'){
                $time_stamp = strtotime("now")-3600; //1 hour in seconds
            }
            if($time == 'past_day'){
                $time_stamp = strtotime("now")-(3600*24); //24 hours in seconds
            }
            if($time == 'past_week'){
                $time_stamp = strtotime("now")-(3600*24*7); //7days in seconds  
            }
            if($time == 'past_month'){
                $time_stamp = strtotime("now")-(3600*24*30); //30days in seconds  
            }
            if($time_stamp != false){
                $date = date("Y-m-d H:i:s",$time_stamp);
                array_push($conditions,array("Voucher.created >=" => $date)); //Add This AND filtertjie
            }
        }
        

         //SQL-aaize it
        $condition  = preg_replace( '/\*/', '%', $condition);

        array_push($conditions,array("$column LIKE" => "$condition")); //Add This AND filtertjie

        //----Special Clauses for AP's ---------------------
        Configure::load('yfi');
        $auth_info = $this->Session->read('AuthInfo');
        //--Realms only need to be checked only for Access Providers--
        if($auth_info['Group']['name'] == Configure::read('group.ap')){   //They can only see whet the are permitted to see 

            //Access Providers should have a list of Realms
            //Check if there are realms assinged to this user and then build the query form it.
            if(!empty($auth_info['Realms'])){
                $realm_filter = array();
                foreach($auth_info['Realms'] as $realm_line){
                    $name_ends_with = $realm_line['append_string_to_user'];
                    array_push($realm_filter,array("Radcheck.username LIKE" => '%@'.$name_ends_with));
                }
            }

            array_push($conditions,array('or' => $realm_filter));

            //--------------------------
            //Access Providers will by default only view vouchers created by them
            //This makes it nice for branches eg an AP is assigned to a branch and only manages their vouchers
            //A Manager then can view all vouchers inside a realm
            //**PERMISSION 'vouchers/only_view_own'
            //**FUNCTION Only list the vouchers an Access Provider created them self
            if($this->_look_for_right('vouchers/only_view_own')){
                    $user_id = $auth_info['User']['id'];
                    array_push($conditions,array("Voucher.user_id" => $user_id)); //Add This AND filtertjie
            }
        };



        //-------------END Search Filter --------------------------------
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

                $s = "Radcheck.username $sort_order";
            }

             if(preg_match('/profile/',$sort)){

                $s = "Profile.name $sort_order";
            }

             if(preg_match('/creator/',$sort)){

                $s = "User.username $sort_order";
            }

            if(preg_match('/realm/',$sort)){

                $s = "Realm.name $sort_order";
            }

            if(preg_match('/created/',$sort)){

                $s = "Voucher.created $sort_order";
            }

             if(preg_match('/status/',$sort)){

                $s = "Voucher.status $sort_order";
            }

        }
        //-------END Order Clause---------------------------------------
        return $s;
    }

     function _look_for_right($right){

        $auth_data = $this->Session->read('AuthInfo');
        if(array_key_exists($right,$auth_data['Rights'])){

            if($auth_data['Rights'][$right]['state'] == '1'){
                return true;
            }
            return false;
        }
        return false;   //Default
    }

     function _add_batch($name,$realm_id){ 

        $b = array();
        $b['Batch']['id']           = '';
        $b['Batch']['name']         = $name;
        $b['Batch']['realm_id']     = $realm_id;

        $this->Batch->save($b);
        return $this->Batch->id;

    }

    function _get_voucher_detail($voucher_name){

        $voucher_detail = array();

        $qc = $this->Radcheck->findAllByUsername($voucher_name);
        foreach($qc as $item){

            if($item['Radcheck']['attribute'] == 'Cleartext-Password'){
                $voucher_detail['password'] = $item['Radcheck']['value'];
            }

            if($item['Radcheck']['attribute'] == 'Yfi-Voucher'){
                $valid                          = $item['Radcheck']['value'];
                $pieces                         = explode('-',$valid);
                //$voucher_detail['days_valid']   = $pieces[0].' Days '.$pieces[1].' Hours '.$pieces[2].' Minutes ';
                if($pieces[0] != 0){
                    if($pieces[0] >= 2){
                        $voucher_detail['days_valid']   = $pieces[0].' '.gettext('days from first log-in');
                    }else{
                        $voucher_detail['days_valid']   = $pieces[0].' '.gettext('day from first log-in');
                    }
                }
            }
        }

        $qr = $this->Radreply->findAllByUsername($voucher_name);
        foreach($qr as $item){
            if($item['Radreply']['attribute'] == 'WISPr-Session-Terminate-Time'){
                $term_time  = $item['Radreply']['value'];
                $pieces      = explode('T',$term_time);
                $voucher_detail['expiry_date'] = $pieces[0];
            }
        }

        return $voucher_detail;
    }


    function _get_generic_pdf_data($list_of_vouchers){

        $pdf_structure = array();

        

        foreach($list_of_vouchers as $id){

            $qr = $this->Voucher->findById($id);
            //print_r($qr);
            $realm_name     = $qr['Realm']['name'];
            $profile_name   = $qr['Profile']['name'];
            $voucher_name   = $qr['Radcheck']['username'];

            if(!array_key_exists($realm_name,$pdf_structure)){
                $pdf_structure[$realm_name] = array();
            }
            //Prime the 'detail' for the Realm
            if(!array_key_exists ('detail',$pdf_structure[$realm_name])){

                $pdf_structure[$realm_name]['detail'] = $qr['Realm'];
            }

            //Prime the 'profiles' for the Realm
            if(!array_key_exists ('profiles',$pdf_structure[$realm_name])){
                $pdf_structure[$realm_name]['profiles']= array();
            }

            if(!array_key_exists($profile_name,$pdf_structure[$realm_name]['profiles'])){

                $pdf_structure[$realm_name]['profiles'][$profile_name]['detail']    = $this->_profile_attributes($profile_name);
            }

            $pdf_structure[$realm_name]['profiles'][$profile_name]['vouchers'][$voucher_name] = $this->_get_voucher_detail($voucher_name);


            //$username   = $qr['Radcheck']['username'];
            //$voucher_detail[$username] = $this->_get_voucher_detail($username);
        }

        return $pdf_structure;

    }

    function _profile_attributes($profile_name){

        $profile_attributes = array();
        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupcheck->find('all',array('conditions' => array('Radgroupcheck.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupcheck']['attribute'];
            $value      = $item['Radgroupcheck']['value'];
            array_push($profile_attributes,array('attribute' => $attribute, 'value' => $value));
        }

        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupreply->find('all',array('conditions' => array('Radgroupreply.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupreply']['attribute'];
            $value      = $item['Radgroupreply']['value'];
            array_push($profile_attributes,array('attribute' => $attribute, 'value' => $value));
        }

        return $profile_attributes;
    }


    function _diff_in_time($date_start,$date_end=''){

        if($date_end == ''){

            $dateTime       = new DateTime("now");
            $date_end       = $dateTime->format("Y-m-d H:i:s");
        }

        //Get the difference between it:
        $diff = abs(strtotime($date_end)-strtotime($date_start));
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


}
?>
