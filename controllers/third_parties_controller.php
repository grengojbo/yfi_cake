<?
class ThirdPartiesController extends AppController {
    var $name       = 'ThirdParties';
    var $helpers    = array('Javascript');

    var $uses       = array(
            'User','Group',
            'UserRealm','Realm',
            'Radcheck','Radreply',
            'Profile','Radusergroup',
            'Radacct','Extra',
            'Radgroupcheck','NotificationDetail',
            'Radacct','Na',
            'Credit', 'Action',
            'Device', 'NaRealm',
    );

    var $components = array('Json','Formatter','CmpPermanent','CmpVoucher');    //Add the locker component

    function beforeFilter() {
       $this->Auth->allow(
		'json_add_narealm',
                'json_usage_check',
                'json_create_voucher',
                'json_create_permanent',
                'json_change_password',
                'json_voucher_name',
                'json_nat_action',
                'json_add_internet_credit',
                'json_delete_permanent',
                'json_add_device_to_user'
        );
    }

    function json_add_narealm(){

	// add a realm to nas device
        $this->layout   = 'ajax'; //To send JSON from the web server
        $key_master     = '12345';
        $json_return    = array();

        //Added Security!
        $request_from   = $_SERVER["REMOTE_ADDR"];      //Only allow request to come from specified server
        if($request_from != '127.0.0.1'){
            $this->set('json_return',$this->Json->permFail());
            return;
        }

        //Check if the callback is defined
        if(array_key_exists('key',$this->params['url'])){

            if($this->params['url']['key'] != $key_master){
                $this->set('json_return',$this->Json->permFail());
                return;
            }
        }

       //Make sure all the required fields are included. Return on the first failure
       $required_fields = array("nas_id","realm_id");
       foreach($required_fields as $field){
            if(array_key_exists($field,$this->params['url'])){
                if(!preg_match("/.+/",$this->params['url'][$field])){
                    $this->set('json_return',array($field => 'Empty values not allowed'));
                    return;
                }
            }else{
                $this->set('json_return',array($field => 'Required field'));
                return;
            }
        }

        //Passed checks, continue
        $realm_id = $this->params['url']['realm_id'];
        $nas_id     = $this->params['url']['nas_id'];


        $d                          = array();
        $d['NaRealm']['id']         = '';
        $d['NaRealm']['na_id']      = $nas_id;
        $d['NaRealm']['realm_id']   = $realm_id;
        $this->NaRealm->save($d);
        $this->NaRealm->id          = false;

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_nat_action(){

        //===Add certain commands to NAT/heartbeat NAS devices===
        //==This controller action will take a nas_id and new SSID name as arguments to generate a command that will be waiting for the NAS
        //___SAMPLE CALL_____
        //___http://127.0.0.1/c2/yfi_cake/third_parties/json_nat_action?callback=completed&key=12345&nas_id=1&ssid=New+Name

        $this->layout   = 'ajax'; //To send JSON from the web server
        $key_master     = '12345';
        $json_return    = array();

        //Added Security!
        $request_from   = $_SERVER["REMOTE_ADDR"];      //Only allow request to come from specified server
        if($request_from != '127.0.0.1'){
            $this->set('json_return',$this->Json->permFail());
            return;
        }

        //Check if the callback is defined
        if(array_key_exists('key',$this->params['url'])){

            if($this->params['url']['key'] != $key_master){
                $this->set('json_return',$this->Json->permFail());
                return;
            }
        }

       //Make sure all the required fields are included. Return on the first failure
       $required_fields = array("nas_id","command");
       foreach($required_fields as $field){
            if(array_key_exists($field,$this->params['url'])){
                if(!preg_match("/.+/",$this->params['url'][$field])){
                    $this->set('json_return',array($field => 'Empty values not allowed'));
                    return;
                }
            }else{
                $this->set('json_return',array($field => 'Required field'));
                return;
            }
        }

        //Passed checks, continue
        $new_command = urldecode($this->params['url']['command']);
        $nas_id     = $this->params['url']['nas_id'];

        //See if there is not allready a change awaiting
        $count = $this->Action->find('count', array('conditions' => array('Action.na_id' => $nas_id,'Action.status' => 'awaiting','Action.command LIKE' => "%nvram%")));
        if($count > 0){
            $json_return['json']['status']      = 'already';
            $this->set('json_return',$json_return);
            return;
        }

	// check if nas_id exists in db, if doesnt exist then any commands sent will stick until manually removed from db


        $d                      = array();
        $d['Action']['na_id']   = $nas_id;
        $d['Action']['command'] = $new_command;
        $this->Action->save($d);

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


    function json_usage_check(){

        //===Report Usage of User===
        //==This controller action will take a username as argument and depending on the user type will retrun the follwing as padded JSON
        //------Permanent Users--------------------------------------------------------------------------------------------------
        //--- Data Avail (in bytes), Data used (in bytes),Extra CAPS ------------------------------------------------------------
        //--- Time Avail (in seconds), Time used (in seconds),Extra CAPS --------------------------------------------------------
        //-----------------------------------------------------------------------------------------------------------------------

        //----- Vouchers------------------------------------------------------------------------------
        //--- Data used , data avail, time used, time avail, expiry date, current session's stats ----
        //--------------------------------------------------------------------------------------------

        //___SAMPLE CALL_____
        //___http://127.0.0.1/c2/yfi_cake/third_parties/json_usage_check?callback=completed&key=12345&username=dvdwalt@ri___



        $this->layout   = 'ajax'; //To send JSON from the web server
        $key            = '12345';

        $json_return    = array();

        //Check if the callback is defined
        if(array_key_exists('key',$this->params['url'])){
           // $this->set('json_return',$this->Json->permFail());
           // return;
        }

        //Check if the key is defiend and correct
        if(array_key_exists('username',$this->params['url'])){
           // $this->set('json_return',$this->Json->permFail());
           // return;
            $username   = $this->params['url']['username'];

            //----Permanent User Check-------
            $qr         = $this->User->find('first',array(
                                    'conditions'    => array('User.username' => $username),
                                    'recursive'     => -1,
                                    'fields'        => array('User.id','User.cap')
                        ));
            if($qr != ''){
                $id             = $qr['User']['id'];
                if($qr['User']['cap'] == 'prepaid'){
                    $usage_summary  = $this->CmpPermanent->usage_prepaid($id);
                }else{
                    $usage_summary  = $this->CmpPermanent->get_current_usage($username,$id);
                }
                // $this_session   = $this->CmpPermanent->get_this_session($username);
                $json_return['json']['summary']     = $usage_summary;
                $json_return['json']['summary']['type'] = 'permananet';
            }
            //---- End Permanent User Check --------

            //----- Voucher Check ----------
            $qr         = $this->Radcheck->find('first',array(
                                    'conditions'    => array('Radcheck.username' => $username,'Radcheck.attribute' => 'Yfi-Voucher'),
                                    'recursive'     => -1,
                                    'fields'        => array('Radcheck.id','Radcheck.value')
                            ));
            if($qr  != ''){

                $id         = $qr['Radcheck']['id'];
                $value      = $qr['Radcheck']['value'];
               // print_r($qr);
                $usage_summary                          = $this->CmpVoucher->get_current_usage($username,$value);
                 $json_return['json']['summary']        = $usage_summary;
                $json_return['json']['summary']['type'] = 'voucher';
            }
            //---- End Voucher Check -------

        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
        //With what shall we padd it?
        $this->set('json_pad_with',$this->params['url']['callback']);
    }


    function json_voucher_name(){

         //_____ Third Party Hook-up to get the voucher name for a password (password used as a code) ___________________
        //A sample call will look like this:
        //http://127.0.0.1/c2/yfi_cake/third_parties/json_voucher_name?password=1c34fh&callback=completed
        //-- Feedback if exists:
        //completed({"voucher":{"exists":true,"name":"00001@ri"},"json":{"status":"ok"}});
        //-- Feedback if not exists:
        //completed({"voucher":{"exists":false},"json":{"status":"ok"}});
        //________________________________________________________________
         

        $this->layout   = 'ajax'; //To send JSON from the web server
        $key            = '12345';

        $json_return    = array();

        //Check if the callback is defined
        if(array_key_exists('key',$this->params['url'])){
           // $this->set('json_return',$this->Json->permFail());
           // return;
        }

        //Check if the key is defiend and correct
        if(array_key_exists('password',$this->params['url'])){      
            $password   = $this->params['url']['password'];
            //Find the voucher to which this password belong to
            $qr = $this->Radcheck->find('first',array('conditions' => array('Radcheck.attribute' => 'Cleartext-Password','Radcheck.value' => $password)));
            if($qr != ''){
                $voucher_name = $qr['Radcheck']['username'];
                $json_return['voucher']['exists'] = true;
                $json_return['voucher']['name'] = $voucher_name;
            }else{
                $json_return['voucher']['exists'] = false;
            }
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
        //With what shall we padd it?
        $this->set('json_pad_with',$this->params['url']['callback']);
    }


    function json_add_internet_credit(){
        //_____ Third Party Hook-up to create and add internet credit ___________________
        //A sample call will look like this:
        //http://127.0.0.1/c2/yfi_cake/third_parties/json_add_internet_credit/?key=123456789&callback=completed&username=dvdwalt&data=250&data_units=mb&expires=2011-12-26&time=0&realm=Residence+Inn
        //----- data and time can be added with data_units (kb/mb/gb) and time_units (m/h/d) to make it more readable and understandable.
        //________________________________________________________________

        (array_key_exists('callback',$this->params['url']))&&($this->set('json_pad_with',$this->params['url']['callback']));

        $this->layout   = 'ajax'; //To send JSON from the web server
        $key_master     = '123456789';
        $access_provider= 'root';    //The name of the Access Provider that this will be made the creator of

        //Added Security
        $request_from   = $_SERVER["REMOTE_ADDR"];      //Only allow request to come from specified server
        if($request_from != '127.0.0.1'){
            $this->set('json_return',$this->Json->permFail());
            return;
        }

        //============== INITIAL CHECKS =====================================
        //Check if the key that this page is called with is the correct key
        if(array_key_exists('key',$this->params['url'])){
            if($this->params['url']['key'] != $key_master){
                $this->set('json_return',$this->Json->permFail());
                return;
            }
        }else{
            $this->set('json_return',$this->Json->permFail());
            return;
        }


        //Make sure all the required fields are included. Return on the first failure
        $required_fields = array("username","data","time","expires","realm");
        foreach($required_fields as $field){

            if(array_key_exists($field,$this->params['url'])){
                if(!preg_match("/.+/",$this->params['url'][$field])){
                    $this->set('json_return',array($field => 'Empty values not allowed'));
                    return;
                }
            }else{
                $this->set('json_return',array($field => 'Required field'));
                return;
            }
        }

        $credit_info   = array();

        //------ We need to determine some values from those given----
        //== Access Provider's ID & name ===
        $q_r    = $this->User->find('first',array('fields' => array('User.id'),'conditions' => array('User.username' => $access_provider),'recursive' => 0));
        if($q_r == ''){
            $this->set('json_return',array('Access Provider Name' => 'Not in database'));
            return;
        }else{
            $credit_info['user_id']             = $q_r['User']['id'];
        }


        //---- Find the user to whom the credit must be added -----
        //== Realm's ID and append string ==
        $this->loadModel('Realm');
        $q_r    = $this->Realm->find('first', array('fields'=> array('Realm.id','Realm.append_string_to_user'),'conditions' => array('Realm.name' => $this->params['url']['realm']), 'recursive' => 0));
        if($q_r == ''){
            $this->set('json_return',array('Realm Name' => 'Not in database'));
            return;
        }else{
            $credit_info['realm_id']    = $q_r['Realm']['id'];
            $full_username              = $this->params['url']["username"].'@'.$q_r['Realm']['append_string_to_user'];
        }

        //== Username ID & name ===
        $q_r    = $this->User->find('first',array('fields' => array('User.id'),'conditions' => array('User.username' => $full_username),'recursive' => 0));
        if($q_r == ''){
            $this->set('json_return',array('User Name' => "$full_username not in database"));
            return;
        }else{
            $credit_info['used_by_id']             = $q_r['User']['id'];
        }

        //-----Check that the expires is correct-----
        if(array_key_exists('expires',$this->params['url'])){
            if(!preg_match("/\d\d\d\d-\d\d-\d\d/",$this->params['url']['expires'])){
                $this->set('json_return',array('expires' => 'Not correct format'));
                return;
            }
        }else{
            $this->set('json_return',array('expires' => 'Required field'));
            return;
        }

        //------ We need to determine some values from those given----
        //== See if there was data units or time units included ===
        
        $data	= $this->params['url']["data"];
        if(array_key_exists('data_units',$this->params['url'])){
            $du = $this->params['url']["data_units"];
		    if($du == 'kb'){
			    $data = ($data * 1024);			
		    }
		    if($du == 'mb'){
			    $data = ($data * 1024 * 1024);
		    }
		    if($du == 'gb'){
			    $data = ($data * 1024 * 1024 * 1024);
		    }
        }

        //Time multiply
		$time	= $this->params['url']['time'];
        if(array_key_exists('time_units',$this->params['url'])){
            $this->params['url']['time_units'];
		    if($tu == 'm'){
			    $time = ($time * 60);			
		    }
		    if($tu == 'h'){
			    $time = ($time * 60 * 60);
		    }
		    if($tu == 'd'){
			    $time = ($time * 60 * 60 * 24);
		    }
        }

        $credit_info['expires']     = $this->params['url']["expires"];
        $credit_info['time']        = $time;
        $credit_info['data']        = $data;

        $this->loadModel('Credit');
        $qr = $this->Credit->save($credit_info);
       // print_r($qr);

        $this->CmpPermanent->update_user_usage($credit_info['used_by_id']);

        $json_return                            = array();
        $json_return['json']['recieved']        = $this->params['url'];
        $json_return['json']['credit_detail']   = $qr['Credit'];
        $json_return['json']['status']          = 'ok';
        $this->set('json_return',$json_return);
    }


     function json_delete_permanent(){
        //_____ Third Party Hook-up to properly delete permanent users ___________________
        //A sample call will look like this: (accounting_also is an optional and will be true if not defined)
        //http://127.0.0.1/c2/yfi_cake/third_parties/json_delete_permanent/?key=123456789&callback=completed&username=koosie@ri&accounting_also=false
        //________________________________________________________________

        (array_key_exists('callback',$this->params['url']))&&($this->set('json_pad_with',$this->params['url']['callback']));

        $this->layout   = 'ajax'; //To send JSON from the web server
        $key_master     = '123456789';

        //Added Security
        $request_from   = $_SERVER["REMOTE_ADDR"];      //Only allow request to come from specified server
        if($request_from != '127.0.0.1'){
            $this->set('json_return',$this->Json->permFail());
            return;
        }

        //============== INITIAL CHECKS =====================================
        //Check if the key that this page is called with is the correct key
        if(array_key_exists('key',$this->params['url'])){
            if($this->params['url']['key'] != $key_master){
                $this->set('json_return',$this->Json->permFail());
                return;
            }
        }else{
            $this->set('json_return',$this->Json->permFail());
            return;
        }

        //Can only be one of two values - default is true
        $accounting_also = true;
        if(array_key_exists('accounting_also',$this->params['url'])){
            if($this->params['url']['accounting_also'] == 'false'){
                $accounting_also = false;
            }
        }

        //Check that username is correct
        if(array_key_exists('username',$this->params['url'])){
            $username = $this->params['url']['username'];
            if(!preg_match("/.+/",$username)){
                $this->set('json_return',array('username' => 'Empty values not allowed'));
                return;
            }
            //Only usernames with a realm are allowed to be deleted
            if(!preg_match("/.+@/",$username)){
                $this->set('json_return',array('username' => 'Usernames without realms not allowed'));
                return;
            }
        }else{
            $this->set('json_return',array('username' => 'Required field'));
            return;
        }

        if ($this->CmpPermanent->delete_user_by_username($username,$accounting_also) != true){
            $this->set('json_return',array('username' => "Not found"));
            return;
        }

        $json_return['deleted_user']        = $username;
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


    function json_create_permanent(){
        //_____ Third Party Hook-up to create permanent users ___________________
        //A sample call will look like this:
        //http://127.0.0.1/c2/yfi_cake/third_parties/json_create_permanent/?key=123456789&callback=completed&username=koosie&password=koos&profile=Permanent+250M+CAP&realm=Residence+Inn&cap=hard
        //----- Optional extra fields that can be added: name; surname; email; phone;
        //________________________________________________________________


        (array_key_exists('callback',$this->params['url']))&&($this->set('json_pad_with',$this->params['url']['callback']));

        $this->layout   = 'ajax'; //To send JSON from the web server
        $key_master     = '123456789';
        $access_provider= 'root';    //The name of the Access Provider that this will be made the creator of

        //Fail it by default
        $json_return                        = array();
        $json_return['success']             = false;

        //Added Security
        $request_from   = $_SERVER["REMOTE_ADDR"];      //Only allow request to come from specified server
        if($request_from != '127.0.0.1'){
            $json_return['error']   = array('type' => 'Permission','detail' =>$this->Json->permFail());
            $this->set('json_return',$json_return);
            return;
        }

        //============== INITIAL CHECKS =====================================
        //Check if the key that this page is called with is the correct key
        if(array_key_exists('key',$this->params['url'])){
            if($this->params['url']['key'] != $key_master){
                $json_return['error']   = array('type' => 'Permission','detail' =>$this->Json->permFail());
                $this->set('json_return',$json_return);
                return;
            }
        }else{
            $json_return['error']   = array('type' => 'Permission','detail' =>$this->Json->permFail());
            $this->set('json_return',$json_return);
            return;
        }

        //Check that username is correct
        if(array_key_exists('username',$this->params['url'])){
            if(!preg_match("/.+/",$this->params['url']['username'])){
                $json_return['error']   = array('type'=>'username','detail' => 'Empty values not allowed');
                $this->set('json_return',$json_return);
                return;
            }

            //If user has a @realm in the value we will remove it
            if(preg_match("/.+@.{1,}$/",$this->params['url']['username'])){
                $string = $this->params['url']['username'];
                $pattern = "/@.{1,}$/";
                $replacement = '';
                $this->params['url']['username']= preg_replace($pattern, $replacement, $string);
            }
           // $this->params['url']['username'] = 'koos';

        }else{
            $json_return['error']   = array('type'=>'username','detail' => 'Required field');
            $this->set('json_return',$json_return);
            return;
        }

        //Check that password is correct
        if(array_key_exists('password',$this->params['url'])){
            if(!preg_match("/.+/",$this->params['url']['password'])){
                $json_return['error']   = array('type' => 'password', 'detail' => 'Empty values not allowed');
                $this->set('json_return',$json_return);
                return;
            }
        }else{
            $json_return['error']   = array('type' => 'password', 'detail' => 'Required field');
            $this->set('json_return',$json_return);
            return;
        }

        //Check that profile is correct
        if(array_key_exists('profile',$this->params['url'])){
            if(!preg_match("/.+/",$this->params['url']['profile'])){
                $json_return['error']   = array('type' => 'profile', 'detail' => 'Empty values not allowed');
                $this->set('json_return',$json_return);
                return;
            }
        }else{
            $json_return['error']   = array('type' => 'profile', 'detail' => 'Required field');
            $this->set('json_return',$json_return);
            return;
        }

        //Check that realm is correct
        if(array_key_exists('profile',$this->params['url'])){
            if(!preg_match("/.+/",$this->params['url']['realm'])){
                $json_return['error']   = array('type' => 'realm','detail' => 'Empty values not allowed');
                $this->set('json_return',$json_return);
                return;
            }
        }else{
            $json_return['error']   = array('type' => 'realm', 'detail' => 'Required field');
            $this->set('json_return',$json_return);
            return;
        }

        //Check that cap is correct
        if(array_key_exists('cap',$this->params['url'])){
            if(!preg_match("/hard|soft|prepaid/",$this->params['url']['cap'])){
                
                $json_return['error']   = array('type' => 'cap', 'detail'  => 'Cap should be hard, soft or prepaid');
                $this->set('json_return',$json_return);
                return;
            }
        }else{
            $json_return['error']   = array('type' => 'cap', 'detail' => 'Required field');
            $this->set('json_return',$json_return);
            return;
        }


        $permanent_info   = array();

        //------ We need to determine some values from those given----
        //== Access Provider's ID & name ===
        $q_r    = $this->User->find('first',array('fields' => array('User.id'),'conditions' => array('User.username' => $access_provider),'recursive' => 0));
        if($q_r == ''){

            $json_return['error']   = array('type' => 'Access Provider Name','detail' => 'Not in database');
            $this->set('json_return',$json_return);
            return;
        }else{
            $permanent_info['user_id']          = $q_r['User']['id'];
            $permanent_info['access_provider']  = $access_provider;
        }

        //== Profile's ID  & name ==
        $this->loadModel('Profile');
        $q_r    = $this->Profile->find('first', array('fields'=> array('Profile.id'),'conditions' => array('Profile.name' => $this->params['url']['profile']), 'recursive' => 0));
        if($q_r == ''){

            $json_return['error']   = array('type' =>'Profile Name', 'detail' => 'Not in database');
            $this->set('json_return',$json_return);
            return;
           
        }else{
            $permanent_info['profile_id']    = $q_r['Profile']['id'];
            $permanent_info['profile_name']  = $this->params['url']['profile'];
        }

        //== Realm's ID and append string ==
        $this->loadModel('Realm');
        $q_r    = $this->Realm->find('first', array('fields'=> array('Realm.id','Realm.append_string_to_user'),'conditions' => array('Realm.name' => $this->params['url']['realm']), 'recursive' => 0));
        if($q_r == ''){
            $json_return['error']   = array('type' => 'Realm Name', 'detail' => 'Not in database');
            $this->set('json_return',$json_return);
            return;
        }else{
            $permanent_info['realm_id']    = $q_r['Realm']['id'];
            $permanent_info['realm_name']  = $q_r['Realm']['append_string_to_user'];
        }

        $permanent_info['username']     = $this->params['url']['username'];
        $permanent_info['password']     = $this->params['url']['password'];
        $permanent_info['cap']          = $this->params['url']['cap'];

        //-- EXPIRE -- 
        //=====Get a default value for expire_on (We can add a IF clause to overwrite this (eg expire an account in an hour)
        Configure::load('yfi');
        $exp_human = Configure::read('permanent_user.expire_on');
        $pieces     = explode("-",$exp_human);
        $y          = $pieces[0];
        $m          = $pieces[1];
        $d          = $pieces[2];
        $exp_unix   = mktime(0, 0, 0, $m, $d, $y);
        $permanent_info['expire_on']    = $exp_unix;

        //--Fast users that register get an hour free---
        Configure::load('yfi');
        $fastProfiles = Configure::read('profiles.fast');
        if(in_array($this->params['url']['profile'],$fastProfiles)){    
                    $permanent_info['expire_on']= time()+3600;
        }
        //--- END Fast get an hour free---



        //-- END EXPIRE --

        //Optional add-ons
        $optional_fields = array("name","surname","email","phone",'expire_on');
        foreach($optional_fields as $field){
            if(array_key_exists($field,$this->params['url'])){
                //Only if the field contains something
                if(preg_match("/.+/",$this->params['url'][$field])){
                    $permanent_info[$field]          = $this->params['url'][$field];
                }
            }
        }

       // print_r($permanent_info);

        //Check if the user's name is not already taken
        $full_user = $this->params['url']['username'].'@'.$permanent_info['realm_name'];
        $count = $this->User->find('count',array('conditions' => array('User.username' => $full_user)));
        if($count > 0){
            $json_return['error']   = array('type'=> 'Username error', 'detail' => "Username ".$this->params['url']['username']." ($full_user) already in use");
            $this->set('json_return',$json_return);
            return;
        }

        //Create the permanent user
        $new_user_id                        = $this->CmpPermanent->add_permanent($permanent_info);
        $permanent_info['user_id']          = $new_user_id;
        $json_return['success']             = true;
        $json_return['json']['status']      = 'ok';
        $json_return['user']                = $permanent_info;
        $this->set('json_return',$json_return);
    }

    function json_create_voucher(){

        //_____ Third Party Hook-up to create vouchers ___________________
        //A sample call will look like this:
        //http://127.0.0.1/c2/yfi_cake/third_parties/json_create_voucher/?key=123456789&voucher_value=1-00-00-00&profile=Voucher+10M+CAP&expires=2010-07-01&precede=sms&realm=Residence+Inn
        //________________________________________________________________

        //print_r($this->params['url']);

        $this->layout   = 'ajax'; //To send JSON from the web server

        //___ Some required values -> Change to suit your setup ____
        $key_master     = '123456789';
        $access_provider= 'root';    //The name of the Access Provider that this will be made the creator of
        //Added Security!
        $request_from   = $_SERVER["REMOTE_ADDR"];      //Only allow request to come from specified server
        if($request_from != '127.0.0.1'){
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //__________________________________________________________


        //============== INITIAL CHECKS =====================================
        $precede = '';
        //Check if the key that this page is called with is the correct key
        if(array_key_exists('key',$this->params['url'])){
            if($this->params['url']['key'] != $key_master){
                $this->set('json_return',$this->Json->permFail());
                return;
            }
        }else{
            $this->set('json_return',$this->Json->permFail());
            return;
        }

        //Check that the voucher_value is correct
        if(array_key_exists('voucher_value',$this->params['url'])){
            if(!preg_match("/\d-\d\d-\d\d-\d\d/",$this->params['url']['voucher_value'])){
                $this->set('json_return',array('voucher_value' => 'Not correct format'));
                return;
            }
        }else{
            $this->set('json_return',array('voucher_value' => 'Required field'));
            return;
        }

        //Check that the profile is present
        if(!array_key_exists('profile',$this->params['url'])){
            $this->set('json_return',array('profile' => 'Required field'));
            return;
        }

        //Check that the expires is correct
        if(array_key_exists('expires',$this->params['url'])){
            if(!preg_match("/\d\d\d\d-\d\d-\d\d/",$this->params['url']['expires'])){
                $this->set('json_return',array('expires' => 'Not correct format'));
                return;
            }
        }

        if(array_key_exists('precede',$this->params['url'])){
            $precede = $this->params['url']['precede'];
        }

        //Check that the realm is present
        if(!array_key_exists('realm',$this->params['url'])){
            $this->set('json_return',array('realm' => 'Required field'));
            return;
        }
        //========= END CHECKS ==========================================

        $this->loadModel('Voucher');
        $this->loadModel('Radusergroup');

        $voucher_info   = array();

        //------ We need to determine some values from those given----
        //== Access Provider's ID & name ===
        $q_r    = $this->User->find('first',array('fields' => array('User.id'),'conditions' => array('User.username' => $access_provider),'recursive' => 0));
        if($q_r == ''){
            $this->set('json_return',array('Access Provider Name' => 'Not in database'));
            return;
        }else{
            $voucher_info['user_id']    = $q_r['User']['id'];
            $voucher_info['access_provider'] = $access_provider;
        }

        //== Profile's ID  & name ==
        $this->loadModel('Profile');
        $q_r    = $this->Profile->find('first', array('fields'=> array('Profile.id'),'conditions' => array('Profile.name' => $this->params['url']['profile']), 'recursive' => 0));
        if($q_r == ''){
            $this->set('json_return',array('Profile Name' => 'Not in database'));
            return;
        }else{
            $voucher_info['profile_id']    = $q_r['Profile']['id'];
            $voucher_info['profile_name']  = $this->params['url']['profile'];
        }

        //== Realm's ID and append string ==
        $this->loadModel('Realm');
        $q_r    = $this->Realm->find('first', array('fields'=> array('Realm.id','Realm.append_string_to_user'),'conditions' => array('Realm.name' => $this->params['url']['realm']), 'recursive' => 0));
        if($q_r == ''){
            $this->set('json_return',array('Realm Name' => 'Not in database'));
            return;
        }else{
            $voucher_info['realm_id']    = $q_r['Realm']['id'];
            $voucher_info['realm_name']  = $q_r['Realm']['append_string_to_user'];
        }

        $voucher_info['precede']    = $precede;
        if(array_key_exists('expires',$this->params['url'])){
            $timestamp     = strtotime($this->params['url']['expires']);
            $iso_format    = date('c',$timestamp);
            $voucher_info['iso_format'] = $iso_format;
        }
        $voucher_info['yfi_voucher']= $this->params['url']['voucher_value'];        
        //print_r($voucher_info);

        //Create the voucher
        $return_data = $this->CmpVoucher->add_voucher($voucher_info);

        $json_return                        = array();
        $json_return['json']['status']      = 'ok';
        $json_return['voucher']['username'] = $return_data['username'];
        $json_return['voucher']['password'] = $return_data['password'];
        $json_return['voucher']['id']       = $return_data['voucher_id'];
        $this->set('json_return',$json_return);
    }


    function json_change_password(){

        //_____ Third Party Hook-up to change passwords ___________________
        //A sample call will look like this:
        //http://127.0.0.1/c2/yfi_cake/third_parties/json_change_password/?key=123456789&username=dvdwalt@ri&password=newpassword
        //________________________________________________________________

        //print_r($this->params['url']);

        $this->layout   = 'ajax'; //To send JSON from the web server

        //___ Some required values -> Change to suit your setup ____
        $key_master     = '123456789';
        $access_provider= '3rd_sms';    //The name of the Access Provider that this will be made the creator of
        //Added Security!
        $request_from   = $_SERVER["REMOTE_ADDR"];      //Only allow request to come from specified server
        if($request_from != '127.0.0.1'){
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //__________________________________________________________

        //Check if the key that this page is called with is the correct key
        if(array_key_exists('key',$this->params['url'])){
            if($this->params['url']['key'] != $key_master){
                $this->set('json_return',$this->Json->permFail());
                return;
            }
        }else{
            $this->set('json_return',$this->Json->permFail());
            return;
        }

        //Check that the username is present
        if(!array_key_exists('username',$this->params['url'])){
            $this->set('json_return',array('username' => 'Required field'));
            return;
        }

        //Check that the password is present
        if(!array_key_exists('password',$this->params['url'])){
            $this->set('json_return',array('password' => 'Required field'));
            return;
        }

        //Check if we can change the password:
        $username = $this->params['url']['username'];
        $password = $this->params['url']['password'];
        $ret_code = $this->CmpPermanent->change_password($username,$password);

        $json_return                        = array();

        $json_return['password_changed']['username'] = $username;
        $json_return['password_changed']['password'] = $password;
    
        if($ret_code == 0){   
            $json_return['password_changed']['success'] = True;
        }else{
            $json_return['password_changed']['success'] = False;
        }

        $json_return['json']['status']      = 'ok'; 
        $this->set('json_return',$json_return);
    }

    function json_add_device_to_user(){

        //_____ Third Party Hook-up to add device to permanent user ___________________
        //A sample call will look like this:
        //http://127.0.0.1/c2/yfi_cake/third_parties/json_add_device_to_user/?key=123456789&username=dvdwalt@ri&device_mac=00-00-27-3B-84-AA&device_description=iPad
        //________________________________________________________________

        //print_r($this->params['url']);

        $this->layout   = 'ajax'; //To send JSON from the web server

        //___ Some required values -> Change to suit your setup ____
        $key_master     = '123456789';
        $access_provider= '3rd_sms';    //The name of the Access Provider that this will be made the creator of
        //Added Security!
        $request_from   = $_SERVER["REMOTE_ADDR"];      //Only allow request to come from specified server
        if($request_from != '127.0.0.1'){
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //__________________________________________________________

        //Check if the key that this page is called with is the correct key
        if(array_key_exists('key',$this->params['url'])){
            if($this->params['url']['key'] != $key_master){
                $this->set('json_return',$this->Json->permFail());
                return;
            }
        }else{
            $this->set('json_return',$this->Json->permFail());
            return;
        }

        //Check that the username is present
        if(!array_key_exists('username',$this->params['url'])){
            $this->set('json_return',array('username' => 'Required field'));
            return;
        }

        //Check that the device_mac is present
        if(!array_key_exists('device_mac',$this->params['url'])){
            $this->set('json_return',array('device_mac' => 'Required field'));
            return;
        }

        //Check that the device_description is present
        if(!array_key_exists('device_description',$this->params['url'])){
            $this->set('json_return',array('device_description' => 'Required field'));
            return;
        }

        $username   = $this->params['url']['username'];
        $mac        = $this->params['url']['device_mac'];
        $descr      = $this->params['url']['device_description'];

        //load the user
        $this->loadModel('User');
        $qr = $this->User->findByUsername($username);
        if($qr != ''){
            $user_id = $qr['User']['id'];
            //Change tha MAC to contain : instead of -
            $mac = str_replace("-",":",$mac);
            $this->loadModel('Device');
            $d = array();
            $d['Device']['name']        = $mac;
            $d['Device']['description'] = $descr;
            $d['Device']['user_id']     = $user_id;
            $this->Device->save($d);
        }else{
            $this->set('json_return',array('User not found' => $username));
            return;
        }

        $json_return['json']['status']      = 'ok'; 
        $this->set('json_return',$json_return);
    }


}
?>
