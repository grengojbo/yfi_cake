<?
class ThirdPartiesController extends AppController {
    var $name       = 'ThirdParties';
    var $helpers    = array('Javascript');

    var $uses       = array('User','Group', 'UserRealm','Realm','Radcheck','Radreply','Profile','Radusergroup','Radacct','Extra','Radgroupcheck','NotificationDetail','Radacct','Na','Credit');

    var $components = array('Json','Formatter','CmpPermanent','CmpVoucher');    //Add the locker component

    function beforeFilter() {
       $this->Auth->allow('json_usage_check','json_create_voucher','json_create_permanent','json_change_password' );
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


    function json_create_permanent(){
        //_____ Third Party Hook-up to create permanent users ___________________
        //A sample call will look like this:
        //http://127.0.0.1/c2/yfi_cake/third_parties/json_create_permanent/?key=123456789&callback=completed&username=koosie&password=koos&profile=Permanent+250M+CAP&realm=Residence+Inn&cap=hard
        //________________________________________________________________


        (array_key_exists('callback',$this->params['url']))&&($this->set('json_pad_with',$this->params['url']['callback']));

        $this->layout   = 'ajax'; //To send JSON from the web server
        $key_master     = '123456789';
        $access_provider= '3rd_sms';    //The name of the Access Provider that this will be made the creator of

        //Added Security!
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

        //Check that username is correct
        if(array_key_exists('username',$this->params['url'])){
            if(!preg_match("/.+/",$this->params['url']['username'])){
                $this->set('json_return',array('username' => 'Empty values not allowed'));
                return;
            }
        }else{
            $this->set('json_return',array('username' => 'Required field'));
            return;
        }

        //Check that password is correct
        if(array_key_exists('password',$this->params['url'])){
            if(!preg_match("/.+/",$this->params['url']['password'])){
                $this->set('json_return',array('password' => 'Empty values not allowed'));
                return;
            }
        }else{
            $this->set('json_return',array('password' => 'Required field'));
            return;
        }

        //Check that profile is correct
        if(array_key_exists('profile',$this->params['url'])){
            if(!preg_match("/.+/",$this->params['url']['profile'])){
                $this->set('json_return',array('profile' => 'Empty values not allowed'));
                return;
            }
        }else{
            $this->set('json_return',array('profile' => 'Required field'));
            return;
        }

        //Check that realm is correct
        if(array_key_exists('profile',$this->params['url'])){
            if(!preg_match("/.+/",$this->params['url']['realm'])){
                $this->set('json_return',array('realm' => 'Empty values not allowed'));
                return;
            }
        }else{
            $this->set('json_return',array('realm' => 'Required field'));
            return;
        }

        //Check that cap is correct
        if(array_key_exists('cap',$this->params['url'])){
            if(!preg_match("/hard|soft|prepaid/",$this->params['url']['cap'])){
                $this->set('json_return',array('cap' => 'Cap should be hard, soft or prepaid'));
                return;
            }
        }else{
            $this->set('json_return',array('cap' => 'Required field'));
            return;
        }


        $permanent_info   = array();

        //------ We need to determine some values from those given----
        //== Access Provider's ID & name ===
        $q_r    = $this->User->find('first',array('fields' => array('User.id'),'conditions' => array('User.username' => $access_provider),'recursive' => 0));
        if($q_r == ''){
            $this->set('json_return',array('Access Provider Name' => 'Not in database'));
            return;
        }else{
            $permanent_info['user_id']          = $q_r['User']['id'];
            $permanent_info['access_provider']  = $access_provider;
        }

        //== Profile's ID  & name ==
        $this->loadModel('Profile');
        $q_r    = $this->Profile->find('first', array('fields'=> array('Profile.id'),'conditions' => array('Profile.name' => $this->params['url']['profile']), 'recursive' => 0));
        if($q_r == ''){
            $this->set('json_return',array('Profile Name' => 'Not in database'));
            return;
        }else{
            $permanent_info['profile_id']    = $q_r['Profile']['id'];
            $permanent_info['profile_name']  = $this->params['url']['profile'];
        }

        //== Realm's ID and append string ==
        $this->loadModel('Realm');
        $q_r    = $this->Realm->find('first', array('fields'=> array('Realm.id','Realm.append_string_to_user'),'conditions' => array('Realm.name' => $this->params['url']['realm']), 'recursive' => 0));
        if($q_r == ''){
            $this->set('json_return',array('Realm Name' => 'Not in database'));
            return;
        }else{
            $permanent_info['realm_id']    = $q_r['Realm']['id'];
            $permanent_info['realm_name']  = $q_r['Realm']['append_string_to_user'];
        }

        $permanent_info['username']     = $this->params['url']['username'];
        $permanent_info['password']     = $this->params['url']['password'];
        $permanent_info['cap']          = $this->params['url']['cap'];

       // print_r($permanent_info);

        //Check if the user's name is not already taken
        $full_user = $this->params['url']['username'].'@'.$permanent_info['realm_name'];
        $count = $this->User->find('count',array('conditions' => array('User.username' => $full_user)));
        if($count > 0){
            $this->set('json_return',array('Username error' => 'Already in use'));
            return;
        }

        //Create the permanent user
        $return_data = $this->CmpPermanent->add_permanent($permanent_info);
        $json_return                        = array();
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
        $access_provider= '3rd_sms';    //The name of the Access Provider that this will be made the creator of
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
        }else{
            $this->set('json_return',array('expires' => 'Required field'));
            return;
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
        $timestamp     = strtotime($this->params['url']['expires']);
        $iso_format    = date('c',$timestamp);
        $voucher_info['yfi_voucher']= $this->params['url']['voucher_value'];
        $voucher_info['iso_format'] = $iso_format;
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

}
?>