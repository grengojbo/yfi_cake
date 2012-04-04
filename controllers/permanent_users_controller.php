<?
class PermanentUsersController extends AppController {
    var $name       = 'PermanentUsers';
    var $uses       = array('User','Group', 'UserRealm','Realm','Radcheck','Radreply','Profile','Radusergroup','Radacct','Extra','Radgroupcheck','NotificationDetail','Radacct','Na','Device','Credit');
    var $components = array('Session','Dojolayout','Rights','Json','Formatter','SwiftMailer','Kicker','CmpPermanent','CmpNote');    //Add the locker component

   // var $scaffold;

     //------------------------------------------------------------------
    //--- List of rights that can be tweaked ---------------------------
    //------------------------------------------------------------------
    /*
        1.) json_index()
        2.) json_add()
        3.) json_del()
        4.) json_edit()
        5.) csv()
    */
    //-----------------------------------------------------------------

   
    //-----AP CRUD Functions-----------------
    function json_index(){

        $this->layout   = 'ajax';                     //To send JSON from the web server
        $json_return    = array();

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------


         //--A Typical request will look like this:-------------------------------------------------------
        //--http://127.0.0.1/cake/yfi/permanent_users/json_index?username=a*&start=80&count=40&sort=username----
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


        $items = array();

        $list   = $this->User->find('all',array(
                                                    'conditions' => $conditions
                                                    )
                                                );

        //Required for the QueryDataStore (Dojo)
        $json_return['numRows']    = count($list);

        //Now we filted only the required page
        if(($start != '')&($count != '')){

             $list   = $this->User->find('all',array(
                                                    'conditions'    => $conditions,
                                                    'limit'         => $count,
                                                    'page'          => $page,
                                                    'order'         => $sort 
                                                    )
                                                );

        }
        //print_r($q_r);

        foreach($list as $item){

            array_push($items,array(
                                    'id'        => $item['User']['id'],
                                    'username'  => $item['User']['username'],
                                    'name'      => $item['User']['name'],
                                    'surname'   => $item['User']['surname'],
                                    'phone'     => $item['User']['phone'],
                                    'email'     => $item['User']['email'],
                                    'active'    => $item['User']['active'],
                                    'data'      => $item['User']['data'],
                                    'time'      => $item['User']['time'],
                                    'profile'   => $item['Profile']['name'],
                                    'realm'     => $item['Realm']['name'],
                                    'creator'   => $item['Creator']['username']
                            ));
        }

        //---Prepare the JSON--------------------
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = $items;
        //-----------------------------------------

        $this->set('json_return',$json_return);
    }


    function json_add($type){

        $this->layout = 'ajax';                     //To send JSON from the web server

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        //Get the optional Permanent User Specifics
        $realm_id       = $this->params['form']['realm'];
        $realm          = $this->Realm->findById($realm_id);
        $realm_name     = $realm['Realm']['append_string_to_user'];
        $realm_name_full= $realm['Realm']['name'];
        $username       = $this->params['form']['username'].'@'.$realm_name;

        $profile_id     = $this->params['form']['profile'];
        $profile        = $this->Profile->findById($profile_id);
        $profile_name   = $profile['Profile']['name'];

        //Check if there is perhaps not already such a user
        $radcheck_id   = $this->_add_entry('Radcheck',$username,'Cleartext-Password',$this->params['form']['password']);

        $json_return = array();
        if($radcheck_id != ''){

            //Add the profile (group)
            $this->_add_radusergroup($username,$profile_name);

            $d['User']['username']     = $username;
            $d['User']['password']     = $this->Auth->password($this->params['form']['password']);
            $d['User']['name']         = $this->params['form']['name'];
            $d['User']['surname']      = $this->params['form']['surname'];
            $d['User']['address']      = $this->params['form']['address'];
            $d['User']['phone']        = $this->params['form']['phone'];
            $d['User']['email']        = $this->params['form']['email'];
            $d['User']['language_id']  = $this->params['form']['language'];
            if(array_key_exists('active',$this->params['form'])){
                $d['User']['active']   = '1';
            }else{
                $d['User']['active']   = '0';
            }
            $d['User']['cap']           = $this->params['form']['cap'];
            $d['User']['group_id']      = $this->_GroupUserId();
            $d['User']['radcheck_id']   = $radcheck_id;
            $d['User']['profile_id']    = $this->params['form']['profile'];
            $d['User']['user_id']       = $this->Auth->user('id');
            $d['User']['realm_id']      = $realm_id;

            $this->User->save($d);
            $user_id = $this->User->id;
            $json_return['json']['status']      = 'ok';
        }else{
            $json_return['json']['status']      = 'duplicates';
        }

        //---Prepare the JSON--------------------
        
        
        $this->set('json_return',$json_return);
    }

    function json_del(){

        $this->layout = 'ajax';

         //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //------------------------------------

        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $user_id = $this->params['url'][$key];
                $u              = $this->User->find('first',array('conditions'=> array('User.id' => $user_id)));
                $username       = $u['User']['username'];
                $this->Radcheck->deleteAll(     array("username"=>"$username"),false);
                $this->Radreply->deleteAll(     array("username"=>"$username"),true);
                $this->Radacct->deleteAll(      array("username"=>"$username"),true);

                //Remove Devices (for MAC authentication)
                $this->Device->deleteAll(       array("Device.user_id" => $user_id),true);
                //Remove all Intenet credits related to this user
                $this->Credit->deleteAll(       array("Credit.used_by_id" => $user_id),true);

                $this->User->del($user_id,true);
                $this->Radusergroup->removeUser($username);
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_view($userId){

        $this->layout = 'ajax';

         //--------Check the rights------
       // if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
        //    $this->set('json_return',$this->Json->permFail());
       //     return;
       // }
        //------------------------------------

        $q_r    = $this->User->findById($userId);

        $json_return['user']['id']          = $q_r['User']['id'];
        $json_return['user']['name']        = $q_r['User']['name'];
        $json_return['user']['surname']     = $q_r['User']['surname'];
        $json_return['user']['address']     = $q_r['User']['address'];
        $json_return['user']['phone']       = $q_r['User']['phone'];
        $json_return['user']['email']       = $q_r['User']['email'];
        $json_return['user']['cap']         = $q_r['User']['cap'];
        $json_return['user']['language_id'] = $q_r['User']['language_id'];
        $json_return['user']['language']    = $q_r['Language']['name'];
         $json_return['user']['profile_id'] = $q_r['User']['profile_id'];
        $json_return['user']['profile']     = $q_r['Profile']['name'];


        //9-3-10 Add Rights to check is a user can do the following
        $right_list = array("update/cap_type","update/name","update/surname","update/address","update/phone","update/email","update/profile");
        foreach($right_list as $right){
            $controll_name  =  preg_replace("/.+\//",'',$right);
            $json_return['right'][$controll_name]   = $this->Rights->LookForRight($right);
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }


	function json_prepaid_list($realm_id){

		//Returns a lsit of prepaid users for specified realm
		 $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok';
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //-- Query the DB ---------------------------------------------
        $r = $this->User->find(
                            'all',
                            array(
                                'conditions'=> array('User.realm_id' => $realm_id,'User.cap' => 'prepaid'),
                                'fields'=>array('User.username', 'User.id'),
                                'order' => 'User.name ASC',
                            )
            );

        //Add the abiltiy to 'unassign' a credit
        array_push($json_return['items'],array('name' => '('.gettext('no-one').')', 'id' => 0, 'selected' => 'selected')); //Select the first one

        $count = 0;
        foreach($r as $entry){
           // if($count == 0){
          //      array_push($json_return['items'],array('name' => $entry['User']['username'], 'id' => $entry['User']['id'], 'selected' => 'selected')); //Select the first one
          //  }else{
                array_push($json_return['items'],array('name' => $entry['User']['username'], 'id' => $entry['User']['id'], 'selected' => ''));
          //  }
            $count ++;
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

	}


    function json_tabs($id){

        //This method will get the rights assigned to the specified user to determine which tabs will be displayed
        $this->layout = 'ajax';

        $json_return = array();
        $json_return['json']['status']  = 'ok';

        #Dummy data
        $json_return['tabs']            = array();
        array_push($json_return['tabs'], array('name' => 'User Detail',     'module' => 'content.PermanentGeneralPersonal', 'file' => 'PermanentGeneralPersonal',   'class' => 'divTabForm'));
        array_push($json_return['tabs'], array('name' => 'Notification',    'module' => 'content.PermanentGeneralNotify',   'file' => 'PermanentGeneralNotify',     'class' => 'divTabForm'));
        array_push($json_return['tabs'], array('name' => 'Usage',           'module' => 'content.PermanentGeneralUsage',    'file' => 'PermanentGeneralUsage',      'class' => 'divTabInTab'));
        //By Default we do not want to show profile attributes to the permanent user
        if($this->Rights->LookForRight('tab/show_profile_attributes')){
            array_push($json_return['tabs'], array('name' => 'Profile Attributes','module' => 'content.PermanentGeneralProfile','file' => 'PermanentGeneralProfile',    'class' => 'divTabInTab'));
        }
        //By Default we do not want to show private attributes to the permanent user
        if($this->Rights->LookForRight('tab/show_private_attributes')){
            array_push($json_return['tabs'], array('name' => 'Private Attributes','module' => 'content.PermanentGeneralPrivate','file' => 'PermanentGeneralPrivate',    'class' => 'divTabInTab'));
        }
        //We can allow a permanent user to add their own devices when they use MAC authentication
        if($this->Rights->LookForRight('tab/show_devices')){
            array_push($json_return['tabs'], array('name' => 'Devices','module' => 'content.PermanentGeneralDevices','file' => 'PermanentGeneralDevices',   'class' => 'divTabInTab'));
        }

        array_push($json_return['tabs'], array('name' => 'Activity',        'module' => 'content.PermanentGeneralActivity', 'file' => 'PermanentGeneralActivity',   'class' => 'divTabInTab'));

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_edit(){

        $this->layout = 'ajax';                     //To send JSON from the web server

        //---- SELF Service ------
        //10-3-10 We add the ability if the user has permissions, that they can update their own data
        //$right_list = array("update/cap_type","update/name","update/surname","update/address","update/phone","update/email");
        $auth_data  = $this->Session->read('AuthInfo');
        if($this->params['form']['id'] == $auth_data['User']['id']){

            $d                  = array();
            $d['User']['id']    = $this->params['form']['id'];
            
            if(($this->Rights->LookForRight('update/cap_type')== True)&(array_key_exists('cap',$this->params['form']))){
                $d['User']['cap']          = $this->params['form']['cap'];
            }

            if(($this->Rights->LookForRight('update/profile')== True)&(array_key_exists('profile',$this->params['form']))){
                $this->json_change_profile();
                $this->CmpNote->addNote(array('user_id' => $this->params['form']['id'],'section_name' => 'Self-service','value' => 'User Profile Changed or updated'));
            }

            if(($this->Rights->LookForRight('update/name')== True)&(array_key_exists('name',$this->params['form']))){
                $d['User']['name']          = $this->params['form']['name'];
            }

            if(($this->Rights->LookForRight('update/surname')== True)&(array_key_exists('surname',$this->params['form']))){
                $d['User']['surname']          = $this->params['form']['surname'];
            }

            if(($this->Rights->LookForRight('update/address')== True)&(array_key_exists('address',$this->params['form']))){
                $d['User']['address']          = $this->params['form']['address'];
            }

            if(($this->Rights->LookForRight('update/phone')== True)&(array_key_exists('phone',$this->params['form']))){
                $d['User']['phone']          = $this->params['form']['phone'];
            }

            if(($this->Rights->LookForRight('update/email')== True)&(array_key_exists('email',$this->params['form']))){
                $d['User']['email']          = $this->params['form']['email'];
            }

            $this->User->save($d);

            //TODO: Add a note informing us the user changed some data (We need to create a cmpNotes component!)
            $this->CmpNote->addNote(array('user_id' => $this->params['form']['id'],'section_name' => 'Self-service','value' => 'User Detail update'));

            $json_return = array();
            $json_return['json']['status']      = 'ok';
            $this->set('json_return',$json_return);
            return;
        }
        //-----END Self Service ------

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------
        $user_id                   = $this->params['form']['id'];

        $d['User']['id']           = $user_id;
        $d['User']['name']         = $this->params['form']['name'];
        $d['User']['surname']      = $this->params['form']['surname'];
        $d['User']['address']      = $this->params['form']['address'];
        $d['User']['phone']        = $this->params['form']['phone'];
        $d['User']['email']        = $this->params['form']['email'];
        $d['User']['cap']          = $this->params['form']['cap'];
        $d['User']['language_id']  = $this->params['form']['language'];

        $this->User->save($d);

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']      = 'ok';

        $this->set('json_return',$json_return);

    }

    function csv(){
        //FIXME: 2B completed

    }

    function json_send_message(){
        //FIXME: 2B completed
        $this->layout = 'ajax';

        $user_id_list   = array();
	    $method         = $this->params['form']['type'];
        $subject        = $this->params['form']['subject'];
        $message        = $this->params['form']['message'];

        foreach(array_keys($this->params['url'])as $key){
            if(preg_match('/^\d/',$key)){
                $id = $this->params['url']["$key"];
                array_push($user_id_list,$id);
            }
        }

        if($method == 'email'){

            $to_list    = array();
            foreach($user_id_list as $item){
                $q_r    = $this->User->findById($item);
                if($q_r){

                    $email = $q_r['User']['email'];
                    if($email != ''){       //Only users with an e-mail addy will be notified!
                        array_push($to_list, $email);
                    }
                }
            }
            //print_r($bcc_list);
            Configure::load('yfi');
            $auth_data = $this->Session->read('AuthInfo');
            $from_email = $auth_data['User']['email'];
            if($from_email == ''){
                $from_email = Configure::read('email.from');
            }
            //Prepare and send message
            $this->SwiftMailer->sendMessage($to_list,$from_email,$subject,$message);

        }
        // print_r($user_id_list);
        $json_return = array();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }


    function json_change_profile(){

        $this->layout   = 'ajax';

        $user_id        = $this->params['form']['id'];
        $profile_id     = $this->params['form']['profile'];

        $qr = $this->User->findById($user_id);
        //Remove the old profile binding
        $username       = $qr['User']['username'];
        $this->Radusergroup->removeUser($username);

        //Add the new profile binding
        $profile        = $this->Profile->findById($profile_id);
        $profile_name   = $profile['Profile']['name'];
        $this->_add_radusergroup($username,$profile_name);

        //Update the user with the new profile id
        $this->User->id = $user_id;
        $this->User->saveField('profile_id', $profile_id);

        $this->CmpPermanent->update_user_usage($user_id);

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }


     function json_private_attributes($id){

        $this->layout   = 'ajax';

        $qr = $this->User->findById($id);
        $username = $qr['User']['username'];

        $json_return['label']           = 'name';
        $json_return['identifier']      = 'id';
        $json_return['items']           = array();

        $qr = $this->Radcheck->findAllByUsername($username);
        foreach($qr as $item){

            $id = 'check_'.$item['Radcheck']['id'];
            array_push($json_return['items'], array('id'=>$id,'name' => $item['Radcheck']['attribute'],'type' =>'Check','op' => $item['Radcheck']['op'],'value' => $item['Radcheck']['value']));
        }

        $qr = $this->Radreply->findAllByUsername($username);
        foreach($qr as $item){

            $id = 'reply_'.$item['Radreply']['id'];
            array_push($json_return['items'], array('id'=>$id,'name' => $item['Radreply']['attribute'],'type' =>'Reply','op' => $item['Radreply']['op'],'value' => $item['Radreply']['value']));
        }

        $json_return['json']['status']  = 'ok';
        $this->set('json_return',$json_return);
    }


    function json_add_private($id,$attribute){

        $this->layout   = 'ajax';

        $qr = $this->User->findById($id);
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


     function json_test_auth($user_id){
        
        $this->layout = 'ajax';

        $qr = $this->User->findById($user_id);
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

    function json_disable(){

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //------------------------------------

        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $user_id = $this->params['url'][$key];
                $u              = $this->User->find('first',array('conditions'=> array('User.id' => $user_id)));
                if($u['User']['active'] =='0'){
                    $this->User->id = $user_id;
                    $this->User->saveField('active', '1');
                }else{
                    $this->User->id = $user_id;
                    $this->User->saveField('active', '0');
                }
                //$username       = $u['User']['username'];
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }


    function json_usage($id,$single= null){

        //See what type of user we are dealing with here:
        $u  = $this->User->find('first',array('conditions'=> array('User.id' => $id)));
        if($u['User']['cap'] == 'prepaid'){
            $this->_usage_prepaid($id,$single);
        }else{
            $this->_usage_cap($id,$single);
        }
    }


     function _usage_prepaid($id,$single= null){

        $this->layout = 'ajax';
        //This method will take the time when the user was created, and calculate the SUM of data as well as the SUM of time in radacct

        //Get the first entry for this user in radacct table - if empty we take today as reference and get the start and end of month for today
        $u  = $this->User->find('first',array('conditions'=> array('User.id' => $id)));
        $username = $u['User']['username'];

        //We also take all the Internet Credits asigned to this user and sum it up. (along with any Yfi-Data / Yfi-Time values
        //Get the user's profile
        $q_r          = $this->Radusergroup->find('first',array('conditions' => array('Radusergroup.username' => $username)));
        $profile      = $q_r['Radusergroup']['groupname'];

        //---Check if Yfi-Data is defined - and how much?----
        $q_r          = $this->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Data')));
        $yfi_data = 'NA';
        if($q_r['Radgroupcheck']['value']  != ''){
            $yfi_data   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Data')));
        if($q_r['Radcheck']['value']     != ''){
            $yfi_data   = $q_r['Radcheck']['value'];
        }

        //---Check if Yfi-Time is defined - and how much?----
        $q_r          = $this->Radgroupcheck->find('first',array('conditions' => array('Radgroupcheck.groupname' => $profile,'Radgroupcheck.attribute' => 'Yfi-Time')));
        $yfi_time = 'NA';
        if($q_r['Radgroupcheck']['value']  != ''){
            $yfi_time   = $q_r['Radgroupcheck']['value'];
        }
        //Check for a personal override
        $q_r        = $this->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Yfi-Time')));
        if($q_r['Radcheck']['value']     != ''){
            $yfi_time   = $q_r['Radcheck']['value'];
        }

        //Get the sum of Internet Credits
        $q_r        = $this->Credit->find('first', array('fields'=>array('SUM(Credit.data) AS data','SUM(Credit.time) AS time'),'conditions' => array('UsedBy.id' => $id)));

       // print_r($q_r);
        ($yfi_data == 'NA')||($yfi_data   = $yfi_data + $q_r[0]['data']);
        ($yfi_time == 'NA')||($yfi_time   = $yfi_time + $q_r[0]['time']);

        $q_r        = $this->Radacct->find('first', array('fields'=>array('SUM(Radacct.acctinputoctets) AS input','SUM(Radacct.acctoutputoctets) AS output','SUM(Radacct.acctsessiontime) AS time'),'conditions' => array('Radacct.username' => $username)));

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
            $time_avail = $this->Formatter->formatted_seconds($yfi_time - $total_time); 
        }

        if($yfi_data == 'NA'){
            $data_avail = 'NA';
        }else{
            $data_avail = $yfi_data - $total_data;
        }

        $items      = array();
        $item = array(
                                'id'            => 1,
                                'start'         => $u['User']['created'], 
                                'end'           => 'NA',
                                'extra_time'    => 'NA',
                                'extra_data'    => 'NA',
                                'time_used'     => $this->Formatter->formatted_seconds($total_time), 
                                'time_avail'    => $time_avail,
                                'data_used'     => $total_data,
                                'data_avail'    => $data_avail,
                        );
        array_push($items, $item);

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = $items;
        //-----------------------------------------

         //--IF $single----- Add graph detail ------
        if($single == 1){

            $json_return['graph_data'] = array();
            $json_return['graph_time'] = array();
            //Get the percent used by user
            $q_r    = $this->User->findById($id);
            $data   = $q_r['User']['data'];
            if($data > 100){
                $data = 100;
            }
            $time   = $q_r['User']['time'];
            if($time > 100){
                $time = 100;
            }

            //--------------DATA-------------------------------------------
            if($data_avail != 'NA'){
                array_push($json_return['graph_data'],array('y' => ($data+1-1),   'text' =>gettext("Used"),        'color' => 'red',   'stroke' => "black", 'tooltip' =>gettext("Used").": <b>".$q_r['User']['data']."</b> %"));
                array_push($json_return['graph_data'],array('y' =>(100- $data),   'text' =>gettext("Available"),   'color' => 'green', 'stroke' => "black", 'tooltip' =>gettext("Available").": <b>".(100-$data)."</b> %"));

            }
            //_____________________________________________________________

            //--------------TIME-------------------------------------------
            if($time_avail != 'NA'){
                array_push($json_return['graph_time'],array('y' => ($time+1-1),     'text' =>gettext("Used"),        'color' => 'red',   'stroke' => "black", 'tooltip' =>gettext("Used").": <b>".$q_r['User']['time']."</b> %"));
                array_push($json_return['graph_time'],array('y' =>(100- $time),     'text' =>gettext("Available"),   'color' => 'green', 'stroke' => "black", 'tooltip' =>gettext("Available").": <b>".(100-$time)."</b> %"));
            }
            //____________________________________________________________

            //We also indicate to the user if they are locked in - then they can kick themselfes
            $count = $this->Radacct->find('count',array('conditions' => array('Radacct.username' => $username,'Radacct.acctstoptime' => null)));
            if($count > 0){
                $json_return['logged_in'] = True;
            }else{
                $json_return['logged_in'] = False;
            } 
        }
        //------------------------------------------

        $this->set('json_return',$json_return);
    }

    function _usage_cap($id,$single= null){

        $this->layout = 'ajax';

        $items = array();
     
        $now    = time();
       
        //Get the first entry for this user in radacct table - if empty we take today as reference and get the start and end of month for today
        $u  = $this->User->find('first',array('conditions'=> array('User.id' => $id)));
        $username = $u['User']['username'];
        //$username = 'lida';

        if($single == null){
            $a  = $this->Radacct->find('first',array('conditions' => array('Radacct.username' => $username),'order' => array('Radacct.acctstarttime ASC')));
            if($a){
                $date_to_start = $a['Radacct']['acctstarttime'];    //First accounting record
            }else{
                $date_to_start = date("Y-m-d H:i:s",$now);        //Now
            }
        }else{
            $date_to_start = date("Y-m-d H:i:s",$now);        //Now
        }

        $counter = 1;

        //Prime the pairs
        $first_pair     = array();
        $first_pair     = $this->CmpPermanent->return_start_and_end_date($date_to_start);
        array_push($items,$this->CmpPermanent->get_usage_during_span($counter,$id,$username,$first_pair));
       // print_r($first_pair);

        //Loop it untill the present time
        while(strtotime($first_pair['end']) < $now){

            $counter++;
            //Add a day to the end time to get to the next month
            $for_next_month         = localtime(strtotime($first_pair['end']),true);
            $start_of_next_month    = date("Y-m-d H:i:s",mktime(12,0,0,$for_next_month['tm_mon']+1,$for_next_month['tm_mday']+4,($for_next_month['tm_year']+1900)));
            $first_pair             = $this->CmpPermanent->return_start_and_end_date($start_of_next_month);
           // print_r($first_pair);
            array_push($items,$this->CmpPermanent->get_usage_during_span($counter,$id,$username,$first_pair));
        }
       // print_r($et);

        //Change the order of the items - so the last one is first
        $items = array_reverse($items);


        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = $items;
        //-----------------------------------------

        //--IF $single----- Add graph detail ------
        if($single == 1){

            $json_return['graph_data'] = array();
            $json_return['graph_time'] = array();
            //Get the percent used by user
            $q_r    = $this->User->findById($id);
            $data   = $q_r['User']['data'];
            if($data > 100){
                $data = 100;
            }
            $time   = $q_r['User']['time'];
            if($time > 100){
                $ime = 100;
            }

            //--------------DATA-------------------------------------------
            if($data != 'NA'){
                array_push($json_return['graph_data'],array('y' => ($data+1-1),   'text' =>gettext("Used"),        'color' => 'red',   'stroke' => "black", 'tooltip' =>gettext("Used").": <b>".$q_r['User']['data']."</b> %"));
                array_push($json_return['graph_data'],array('y' =>(100- $data),   'text' =>gettext("Available"),   'color' => 'green', 'stroke' => "black", 'tooltip' =>gettext("Available").": <b>".(100-$data)."</b> %"));

            }
            //_____________________________________________________________

            //--------------TIME-------------------------------------------
            if($time != 'NA'){
                array_push($json_return['graph_time'],array('y' => ($time+1-1),     'text' =>gettext("Used"),        'color' => 'red',   'stroke' => "black", 'tooltip' =>gettext("Used").": <b>".$q_r['User']['time']."</b> %"));
                array_push($json_return['graph_time'],array('y' =>(100- $time),     'text' =>gettext("Available"),   'color' => 'green', 'stroke' => "black", 'tooltip' =>gettext("Available").": <b>".(100-$time)."</b> %"));
            }
            //____________________________________________________________

            //We also indicate to the user if they are locked in - then they can kick themselfes
            $count = $this->Radacct->find('count',array('conditions' => array('Radacct.username' => $username,'Radacct.acctstoptime' => null)));
            if($count > 0){
                $json_return['logged_in'] = True;
            }else{
                $json_return['logged_in'] = False;
            } 
        }
        //------------------------------------------
        $this->set('json_return',$json_return);
    }

    function json_kick($id){

        //Self help function for users to terminate any open sessions 
        $this->layout = 'ajax';
        $json_return    = array();

        //User can only kick themselves off
        if($this->Auth->user('id') != $id){
            $json_return['json']['status']      = 'error';
            $this->set('json_return',$json_return);
            return;
        }

        //Get the username for $id
        $q_r = $this->User->findById($id);
        $username = $q_r['User']['username'];
        $q_r = $this->Radacct->find('all',array('conditions' => array('Radacct.username' => $username,'Radacct.acctstoptime' => null)));
        if($q_r != ''){
            foreach($q_r as $item){
                $radacctid = $item['Radacct']['radacctid'];
                $this->_kick_user($radacctid);
                usleep(5000);   //Rest half a second
                //Close the setup any way - perhaps it was an orphan session
                $now = date('Y-m-d h:i:s');
                $this->Radacct->id = $radacctid;
                $this->Radacct->saveField('acctstoptime', $now);
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }


    function json_notify_detail($id){

        $this->layout = 'ajax';

        $json_return    = array();

        //Check if there is a notification detail entry for said user
        $q_r = $this->NotificationDetail->findByUserId($id);

        if($q_r != ''){

            $json_return['user']['id']              = $id;
            $json_return['user']['type']            = $q_r['NotificationDetail']['type'];
            $json_return['user']['address1']        = $q_r['NotificationDetail']['address1'];
            $json_return['user']['address2']        = $q_r['NotificationDetail']['address2'];
            $json_return['user']['start']           = $q_r['NotificationDetail']['start'];;
            $json_return['user']['increment']       = $q_r['NotificationDetail']['increment'];;

        }else{

            $q_r = $this->User->findById($id);
            $json_return['user']['id']              = $id;
            $json_return['user']['type']            = 'disabled';
            $json_return['user']['address1']        = $q_r['User']['email'];
            $json_return['user']['address2']        = '';
            $json_return['user']['start']           = 80;
            $json_return['user']['increment']       = 10;
        }

        $right_list = array("notify/type","notify/address1","notify/address2","notify/start","notify/increment");
        foreach($right_list as $right){
            $controll_name  =  preg_replace("/.+\//",'',$right);
            $json_return['right'][$controll_name]   = $this->Rights->LookForRight($right);
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_notify_save(){

        $this->layout = 'ajax';

        $json_return    = array();

        //---- SELF Service ------
        //10-3-10 We add the ability if the user has permissions, that they can update their own data
        //$right_list = array("notify/type","notify/address1","notify/address2","notify/start","notify/increment");
        $auth_data  = $this->Session->read('AuthInfo');
        if($this->params['form']['id'] == $auth_data['User']['id']){

            //Check if there is already an entry for the said user
            $id = $this->params['form']['id'];
            $notify_id = '';

            $q_r    = $this->NotificationDetail->findByUserId($id);
            if($q_r){
                $notify_id = $q_r['NotificationDetail']['id'];
            }

            $d                                      = array();
            $d['NotificationDetail']['id']          = $notify_id;
            $d['NotificationDetail']['user_id']     = $id;
            
            if(($this->Rights->LookForRight('notify/type')== True)&(array_key_exists('type',$this->params['form']))){
                $d['NotificationDetail']['type']        = $this->params['form']['type'];
            }

            if(($this->Rights->LookForRight('notify/address1')== True)&(array_key_exists('address1',$this->params['form']))){
                $d['NotificationDetail']['address1']    = $this->params['form']['address1'];
            }

            if(($this->Rights->LookForRight('notify/address2')== True)&(array_key_exists('address2',$this->params['form']))){
                $d['NotificationDetail']['address2']    = $this->params['form']['address2'];
            }

            if(($this->Rights->LookForRight('notify/start')== True)&(array_key_exists('start',$this->params['form']))){
                $d['NotificationDetail']['start']       = $this->params['form']['start'];
            }

            if(($this->Rights->LookForRight('notify/increment')== True)&(array_key_exists('increment',$this->params['form']))){
                $d['NotificationDetail']['increment']   = $this->params['form']['increment'];
            }

            $this->NotificationDetail->save($d);

            //TODO: Add a note informing us the user changed some data (We need to create a cmpNotes component!)
            $this->CmpNote->addNote(array('user_id' => $this->params['form']['id'],'section_name' => 'Self-service','value' => 'Notification Detail update'));

            $json_return = array();
            $json_return['json']['status']      = 'ok';
            $this->set('json_return',$json_return);
            return;
        }
        //-----END Selef Service ------


        //Check if there is already an entry for the said user
        $id = $this->params['form']['id'];

        $notify_id = '';

        $q_r    = $this->NotificationDetail->findByUserId($id);
        if($q_r){
            $notify_id = $q_r['NotificationDetail']['id'];
        }

        $d['NotificationDetail']['id']          = $notify_id;
        $d['NotificationDetail']['user_id']     = $id;
        $d['NotificationDetail']['type']        = $this->params['form']['type'];
        $d['NotificationDetail']['address1']    = $this->params['form']['address1'];
        $d['NotificationDetail']['address2']    = $this->params['form']['address2'];
        $d['NotificationDetail']['start']       = $this->params['form']['start'];
        $d['NotificationDetail']['increment']   = $this->params['form']['increment'];
        $this->NotificationDetail->save($d);

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


     function json_view_activity($id){

        $this->layout = 'ajax';

        $qr         = $this->User->findById($id);
        $username   = $qr['User']['username'];


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
        $list   = $this->Radacct->find('all',array(
                                                    'conditions'    => array('Radacct.username' => $username)
                                                    )
                                                );

        //Required for the QueryDataStore (Dojo)
        $json_return['numRows']    = count($list);



        $qr = $this->Radacct->find('all',array(
                                        'conditions'    => array('Radacct.username' => $username),
                                        'order'         => array('Radacct.acctstarttime DESC'),
                                        'limit'         => $count,
                                        'page'          => $page
                ));

       // print_r($qr);
 
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

           // $b_tx         = str_pad($bytes_tx,20, "0", STR_PAD_LEFT);
           // $b_rx         = str_pad($bytes_rx,20, "0", STR_PAD_LEFT);
           // $b_total      = str_pad(($bytes_rx+$bytes_tx),20, "0", STR_PAD_LEFT);
            $duration     = $this->_diff_in_time($start_time,$stop_time);
            

            array_push($json_return['items'], array('id' => $id,'mac'=>$client_mac,'ip' => $client_ip,'start_time' => $start_time, 'stop_time' => $stop_time,'duration' => $duration,'bytes_tx' => $bytes_tx, 'bytes_rx' => $bytes_rx, 'bytes_total' => ($bytes_rx+$bytes_tx)));

        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }


    function json_del_activity($user_id){
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
        //---Update usage----
        $this->CmpPermanent->update_user_usage($user_id);
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

//-----------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------
     function json_password(){

        $this->layout = 'ajax';

        //print_r($this->params);

        $d['User']['id']            = $this->params['form']['id'];
        $d['User']['password']      = $this->Auth->password($this->params['form']['password']);
       
        $this->User->save($d);
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    //*****************************************************************************************************
    //----------- Dynamic Actions -------------------------------------------------------------------------
    //*****************************************************************************************************

    function json_actions(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on Users tab------------ 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';

        $json_return['items']             = $this->Dojolayout->actions_for_permanent_users();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_actions_for_user_profile(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a profile view tab (profile sub tab) 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_user_profile();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_actions_for_user_private(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a profile view tab (private sub tab) 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_user_private();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);


    }

     function json_actions_for_user_activity(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a profile view tab (private sub tab) 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_user_activity();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }



    //========================================================================================================================
    //===================== Private Functions ================================================================================
    //========================================================================================================================



    //-----END AP CRUD Functions-------------------

    function _addRealmForAP($userId,$realmId){

        $d['UserRealm']['id']       = '';
        $d['UserRealm']['user_id']  = $userId;
        $d['UserRealm']['realm_id'] = $realmId;

        $this->UserRealm->save($d);

    }

    function _RealmName($realmId){
        
        $q_r    = $this->Realm->findById($realmId);
        $name   = $q_r['Realm']['name'];
        return $name;
    }

    function _GroupUserId(){

        Configure::load('yfi');
        $user_name = Configure::read('group.user');
        $q_r    = $this->Group->findByName($user_name);
        $groupId = $q_r['Group']['id'];
        return $groupId;
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

    function _add_radusergroup($username,$groupname){

        $this->Radusergroup->id =false;
        $rc = array();
        $rc["Radusergroup"]['username']   = $username;
        $rc["Radusergroup"]['groupname']  = $groupname;
        $rc["Radusergroup"]['priority']   = '1';
        $this->Radusergroup->save($rc);
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



     function _returnSearchFilterConditions(){

        //----------------Search Filter ----------------------
        $column;
        $condition;

        if(array_key_exists('username',$this->params['url'])){
            $column    = 'User.username';
            $condition  = $this->params['url']['username'];
        }

        if(array_key_exists('profile',$this->params['url'])){
            $column    = 'Profile.name';
            $condition  = $this->params['url']['profile'];
        }

        if(array_key_exists('creator',$this->params['url'])){
            $column    = 'Creator.username';
            $condition  = $this->params['url']['creator'];
        }

        if(array_key_exists('realm',$this->params['url'])){
            $column    = 'Realm.name';
            $condition  = $this->params['url']['realm'];
        }

        if(array_key_exists('data',$this->params['url'])){

            $column     = 'User.data';
            $condition  = $this->params['url']['data'];
        }

        if(array_key_exists('time',$this->params['url'])){

            $column     = 'User.time';
            $condition  = $this->params['url']['time'];
        }

         //SQL-aaize it
        $condition  = preg_replace( '/\*/', '%', $condition);

        $conditions = array(); //This will grow in complexity

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
                    array_push($realm_filter,array("User.username LIKE" => '%@'.$name_ends_with));
                }
            }

            array_push($conditions,array('or' => $realm_filter));

            //--------------------------
            //Access Providers will by default only view users created by them
            //This makes it nice for branches eg an AP is assigned to a branch and only manages their users
            //A Manager then can view all users inside a realm
            //**PERMISSION 'users/only_view_own'
            //**FUNCTION Only list the users an Access Provider created them self
            if($this->_look_for_right('permanent_users/only_view_own')){       #FIXME Change to users....
                    $user_id = $auth_info['User']['id'];
                    array_push($conditions,array("User.user_id" => $user_id)); //Add This AND filtertjie
            }
        };


        //---- We only list group type 'users'-----------------
        Configure::load('yfi');
        $user_name = Configure::read('group.user');
        array_push($conditions,array("Group.name" => $user_name));
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

                $s = "User.username $sort_order";
            }

             if(preg_match('/profile/',$sort)){

                $s = "Profile.name $sort_order";
            }

             if(preg_match('/creator/',$sort)){

                $s = "Creator.username $sort_order";
            }

            if(preg_match('/realm/',$sort)){

                $s = "Realm.name $sort_order";
            }

            if(preg_match('/data/',$sort)){

                $s = "User.data $sort_order";
            }

             if(preg_match('/time/',$sort)){

                $s = "User.time $sort_order";
            }

            if(preg_match('/active/',$sort)){

                $s = "User.active $sort_order";
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

   
    

    function _kick_user($radacctid){
        //Determine the type of NAS device -
        $q_r = $this->Radacct->findByRadacctid($radacctid);
        //$username = $q_r['Radacct']['username'];
        //Get the IP of the NAS device
        //$nas_ip = $q_r['Radacct']['nasipaddress'];
        $this->Kicker->kick($q_r['Radacct']);
    }


}
?>
