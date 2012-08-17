<?
class UsersController extends AppController {
    var $name       = 'Users';
    var $uses       = array('User','Group', 'UserRealm','Realm','Radcheck','Language');
    var $components = array('Session','Rights','Json');    //Add the locker component

    //var $scaffold;

    function beforeFilter() {

       $this->Auth->allow('json_login_check','json_login','json_languages');
       $this->Auth->logoutRedirect = '/users/json_login_check';
    }

    function index() {

    }

    function login(){

    }

    function json_languages($iso_is_id=false,$iso_name=false){

        $this->layout = 'ajax'; //To send JSON from the web server
        $json_return = array();

         //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //---If the language is set, we change the loacel to that
        if($iso_name != false){
            Configure::load('yfi'); //Load the config file which contains application wide settings / values
            $locale_location    = Configure::read('locale.location');
            putenv("LANG=$iso_name.utf8"); 
            putenv("LANGUAGE=$iso_name.utf8"); 
            setlocale(LC_ALL, "$iso_name.utf8");
            // Set the text domain as 'messages'
            $domain = 'messages';
            bindtextdomain($domain,$locale_location ); 
            textdomain($domain);
        }
        //----------------------------------------------------


        $qr = $this->Language->find('all',array('order'=> 'Language.name ASC' ));
        foreach($qr as $item){

            if($iso_is_id == true){
                $id     = $item['Language']['iso_name'];
            }else{
                $id     = $item['Language']['id'];
            }
            $name   = gettext($item['Language']['name']);
            $iso    = $item['Language']['iso_name'];
            array_push($json_return['items'],array('id' => $id, 'name' => $name, 'iso_name' => $iso));

        }
        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_change_language(){

        $this->layout   = 'ajax'; //To send JSON from the web server
        $json_return    = array();
        $json_return['json']['status']  = 'ok';

        $user_id    = $this->params['form']['id'];
        $lang_id    = $this->params['form']['language'];

        $d = array();
        $d['User']['id']            = $user_id;
        $d['User']['language_id']   = $lang_id;
        $this->User->save($d);

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_login(){

        $this->layout = 'ajax'; //To send JSON from the web server
        $json_return = array();

        if(isset($this->params['form']['Username']) & isset($this->params['form']['Password'])){    //Verify we were called the right way
          
            $data['User']['username'] = $this->params['form']['Username'];
            $data['User']['password'] = $this->params['form']['Password'];
            //The hashPasswords method require that $data['User']['username'] AND $data['User']['password'] be defined in the argument
            $hashedPasswords = $this->Auth->hashPasswords($data);
            if($this->Auth->login($hashedPasswords)){   //The way to do Ajax logins
                
                $json_return['json']['status']  = 'ok';
                $json_return['user']            = $this->Auth->user();
                $user_data                      = $this->Auth->user();
               
                //$q_r                            = $this->Group->findById($user_data['User']['group_id']);
                $q_r                            = $this->User->findById($user_data['User']['id']);
                $group_name                     = $q_r['Group']['name'];

                $json_return['user']['group']   = $group_name;
                $json_return['user']['l_iso']   = $q_r['Language']['iso_name'];
                $json_return['user']['lang']    = $q_r['Language']['name'];
                 //Add a logo if the user is a member of Users or Access Providers
                Configure::load('yfi'); //Load the config file which contains application wide settings / values
                if(($group_name == Configure::read('group.ap'))|($group_name == Configure::read('group.user'))){
                    $json_return['user']['logo_file'] = $this->_getLogoFile($json_return['user']['User']['username'],$json_return['user']['User']['id']);
                }
                //----

                $this->set('json_return',$json_return);
            }else{
                $json_return['json']['status']  = 'error';
                $json_return['json']['detail']  = 'Authentication Failure';
                $this->set('json_return',$json_return);
            }
        }else{
            //Not called the correct way - Inform the user
            $json_return['json']['status']  = 'error';
            $json_return['json']['detail']  = 'HTML Form POST Data Missing';
            $this->set('json_return',$json_return);
        }
    }

    function json_login_check(){

        $this->layout = 'ajax';                     //To send JSON from the web server
        $json_return = array();
        $json_return['json']['status']  = 'ok';     //Not much can go wrong here :)!

        $auth = $this->Auth->user();
        if($auth['User']){
            $json_return['authenticated']   = true;

            //----Optional user info----
            $json_return['user']            = $this->Auth->user();
            $user_data                      = $this->Auth->user();
            $q_r                            = $this->User->findById($user_data['User']['id']);
            $group_name                     = $q_r['Group']['name'];

            $json_return['user']['group']   = $group_name;
            $json_return['user']['l_iso']   = $q_r['Language']['iso_name'];
            $json_return['user']['lang']    = $q_r['Language']['name'];

            //Add a logo if the user is a member of Users or Access Providers
            Configure::load('yfi'); //Load the config file which contains application wide settings / values
            if(($group_name == Configure::read('group.ap'))|($group_name == Configure::read('group.user'))){
                $json_return['user']['logo_file'] = $this->_getLogoFile($json_return['user']['User']['username'],$json_return['user']['User']['id']);
            }
            //-------------------------

        }else{
            $json_return['authenticated']   = false;
        }
        $this->set('json_return',$json_return);
    }

     function logout() {

        //This will log the user out and auto-redirect to the json_login_check page
        //$this->Auth->logoutRedirect = '/users/json_login_check';
        $this->Session->del('AuthInfo');
        //Destroy all other sessions as well
        $this->Session->destroy();

        $this->redirect($this->Auth->logout());
    }


    //-----AP CRUD Functions-----------------
    function json_ap_index($group){

        $this->layout = 'ajax';                     //To send JSON from the web server

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        $items = array();
        Configure::load('yfi');
        $ap_name = Configure::read('group.ap');
        $q_r = $this->User->find('all',array('conditions' => array('Group.name' => $ap_name )));

        foreach($q_r as $item){

            $realms = $this->_getRealmsForUser($item['User']['id']);

            array_push($items,array(
                                    'id'        => $item['User']['id'],
                                    'username'  => $item['User']['username'],
                                    'name'      => $item['User']['name'],
                                    'surname'   => $item['User']['surname'],
                                    'phone'     => $item['User']['phone'],
                                    'email'     => $item['User']['email'],
                                    'active'    => $item['User']['active'],
                                    'realms'    => $realms
                            ));
        }

        //print_r($q_r);

        
        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = $items;
        //-----------------------------------------

        $this->set('json_return',$json_return);
    }


    function json_ap_add($type){

        $this->layout = 'ajax';                     //To send JSON from the web server

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

       
        $d['User']['username']     = $this->params['form']['username'];
        $d['User']['password']     = $this->Auth->password($this->params['form']['password']);
        $d['User']['name']         = $this->params['form']['name'];
        $d['User']['surname']      = $this->params['form']['surname'];
        $d['User']['address']      = $this->params['form']['address'];
        $d['User']['phone']        = $this->params['form']['phone'];
        $d['User']['email']        = $this->params['form']['email'];
        $d['User']['language_id']  = $this->params['form']['language'];
        $d['User']['group_id']     = $this->_GroupAPId(); 


        if(array_key_exists('active',$this->params['form'])){
            $d['User']['active']   = '1';
        }else{
            $d['User']['active']   = '0';
        }

        $this->User->save($d);
        $user_id = $this->User->id;

        
        //-----------------------
        //Build a list of Realms
        $realmIds   = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                array_push($realmIds,$this->params['url'][$key]);
            }
        }
        //--------------------------

        
        //---------------------------------
        //Get the names of the realms / and add them
        $counter = 0;
        $realms  = '';
        foreach($realmIds as $item){


            $this->_addRealmForAP($user_id,$item);  //Add the UserRealm binding

            $r_r = $this->Realm->find('first', array('conditions' => array('Realm.id' => $item)));
            if($counter == 0){
                $realms = $this->_RealmName($item);
            }else{
                $realms = $realms." <br /> ".$this->_RealmName($item);
            }
            $counter++;
        }
        //------------------------------

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']      = 'ok';

        $json_return['ap']['id']            = $user_id;
        $json_return['ap']['username']      = $d['User']['username'];
        $json_return['ap']['name']          = $d['User']['name'];
        $json_return['ap']['surname']       = $d['User']['surname'];
        $json_return['ap']['phone']         = $d['User']['phone'];
        $json_return['ap']['email']         = $d['User']['email'];
        $json_return['ap']['active']        = $d['User']['active'];
        $json_return['ap']['realms']        = $realms;

        $this->set('json_return',$json_return);
        
    }

    function json_ap_del(){

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
                $this->User->del($user_id,true);
                //Delete the realms that this user may have belonged to
                $this->UserRealm->deleteAll(array('UserRealm.user_id'=> $user_id));
                $this->UserRight->deleteAll(array('UserRight.user_id'=> $user_id));
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_ap_view($apId){

        $this->layout = 'ajax';

         //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //------------------------------------

        $q_r    = $this->User->findById($apId);

        $json_return['user']['id']          = $q_r['User']['id'];
        $json_return['user']['username']    = $q_r['User']['username'];
        $json_return['user']['name']        = $q_r['User']['name'];
        $json_return['user']['surname']     = $q_r['User']['surname'];
        $json_return['user']['address']     = $q_r['User']['address'];
        $json_return['user']['phone']       = $q_r['User']['phone'];
        $json_return['user']['email']       = $q_r['User']['email'];
        $json_return['user']['active']      = $q_r['User']['active'];
        $json_return['user']['group_id']    = $q_r['User']['group_id'];
        $json_return['user']['language_id'] = $q_r['User']['language_id'];
        //print_r($q_r);


        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_ap_edit(){

            $this->layout = 'ajax';                     //To send JSON from the web server

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------
        $user_id                   = $this->params['form']['id'];

        $d['User']['id']           = $user_id;
        $d['User']['username']     = $this->params['form']['username'];
        $d['User']['name']         = $this->params['form']['name'];
        $d['User']['surname']      = $this->params['form']['surname'];
        $d['User']['address']      = $this->params['form']['address'];
        $d['User']['phone']        = $this->params['form']['phone'];
        $d['User']['email']        = $this->params['form']['email'];
        $d['User']['language_id']  = $this->params['form']['language'];
        $d['User']['group_id']     = $this->_GroupAPId(); 


        if(array_key_exists('active',$this->params['form'])){
            $d['User']['active']   = '1';
        }else{
            $d['User']['active']   = '0';
        }

        $this->User->save($d);

        //Remove existing UserRealm bindings 
        $this->UserRealm->deleteAll(array('UserRealm.user_id'=> $user_id));

        //-----------------------
        //Build a list of Realms
        $realmIds   = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                array_push($realmIds,$this->params['url'][$key]);
            }
        }
        //--------------------------

        
        //---------------------------------
        //Get the names of the realms / and add them
        $counter = 0;
        $realms  = '';
        foreach($realmIds as $item){


            $this->_addRealmForAP($user_id,$item);  //Add the UserRealm binding

            $r_r = $this->Realm->find('first', array('conditions' => array('Realm.id' => $item)));
            if($counter == 0){
                $realms = $this->_RealmName($item);
            }else{
                $realms = $realms." <br /> ".$this->_RealmName($item);
            }
            $counter++;
        }
        //------------------------------

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']      = 'ok';
        $json_return['ap']['realms']        = $realms;
        $json_return['ap']['active']        = $d['User']['active'];

        $this->set('json_return',$json_return);

    }


     function json_password(){

        $this->layout = 'ajax';

        //print_r($this->params);

        $d['User']['id']            = $this->params['form']['id'];
        $d['User']['password']      = $this->Auth->password($this->params['form']['password']);

        //---------------------------------------------------------------------------------------------
        //If the request is for a group User -> We have to also synch the radcheck table for that user
        $q_r = $this->User->findById($this->params['form']['id']);
        if($q_r){

            $group = $q_r['Group']['name'];
            Configure::load('yfi'); 
            if($group == Configure::read('group.user')){
                //This is a user's password that needs changing
                $username = $q_r['User']['username'];
                $qr = $this->Radcheck->find('first',array('conditions' =>array('Radcheck.username' => $username, 'Radcheck.attribute' => 'Cleartext-Password')));
                $rc_id = $qr['Radcheck']['id'];
                $this->Radcheck->id = $rc_id;
                $this->Radcheck->saveField('value',$this->params['form']['password']);
            }
        }
        //---------------------------------------------------------------------------------------------
       
        $this->User->save($d);
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    //-----END AP CRUD Functions-------------------


    function _getLogoFile($username,$user_id){

        if(preg_match('/^.+@.+/',$username)){
            $pieces = explode('@',$username);
            $qr = $this->Realm->find('first',array('conditions' => array('Realm.append_string_to_user' => $pieces[1])));
            $icon_file = $qr['Realm']['icon_file_name'];
            return $icon_file;
        }else{
            $qr = $this->UserRealm->find('first',array('conditions' => array('UserRealm.user_id' =>$user_id)));
            if($qr != ''){
                $icon_file = $qr['Realm']['icon_file_name'];
            }else{
                $icon_file = 'logo.jpg';
            }
            return $icon_file;
        }
    }


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

    function _GroupAPId(){

        Configure::load('yfi');
        $ap_name = Configure::read('group.ap');
        $q_r    = $this->Group->findByName($ap_name);
        $groupId = $q_r['Group']['id'];
        return $groupId;
    }


    function _getRealmsForUser($userId){

        $q_u = $this->UserRealm->findAllByUserId($userId);

        //---Build the realms ---------------
        $realms_string ='';
        $counter = 0;
        foreach($q_u as $item){

            $this_realm     = $item['Realm']['name'];
            if($counter > 0){
                $realms_string   = $realms_string.' <br /> '.$this_realm;
            }else{
                $realms_string = $this_realm;
            }
        $counter++;
        }
        //---- END Realm Build -----
        return($realms_string);
    }

}
?>
