<?
class NasController extends AppController {
    var $name       = 'Nas';
    var $helpers    = array('Javascript');

    var $components = array('Session','Rights','Json','Dojolayout','Pptpd','Formatter','SwiftMailer');    //Add the locker component
    var $uses       = array('Na','NaRealm','Realm','User','Radacct','NaState','Check','Map','Heartbeat');

    function beforeFilter() {

       $this->Auth->allow('json_nas_map_public');       //Comment out to remove public display of Google Map overlay
     
    }

   // var $scaffold;


    //------------------------------------------------------------------
    //--- List of rights that can be tweaked ---------------------------
    //------------------------------------------------------------------
    /*
        1.) json_del    (use for map as well)
        2.) json_add    (use for map as well)
        3.) json_add_vpn (special)
        4.) json_edit  (use for map as well)
        5.) json_edit_optional

    */
    //-----------------------------------------------------------------


    function json_index($quick=false){  //For the dojo Grid
       
        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //---------------------------------------------------------------------
        $auth_data = $this->Session->read('AuthInfo');
        Configure::load('yfi'); 
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
                $r = $this->Na->find('all',array()); //Get all the NAS devices
        }else{
                $r = $this->Na->find('all',array('conditions' => array('Na.nasname <>' => '127.0.0.1'))); //Get all the NAS devices EXCEPT 127.0.0.1
        }

        if($quick == false){
            //Run a heartbeat test to see if some devices may have pulsed us...
            exec("/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake nasmonitor -only_heartbeat >> /dev/null 2>&1");
        }
        

       // print_r($r);

        //Loop through it and check the user's rights decide whether to display or not
        foreach($r as $entry){

            $na_id      = $entry['Na']['id'];
            $last_state = 'No Data';

            if($entry['Na']['monitor'] == 1){
                //Get the last state
                if(count($entry['NaState']) > 0){
                    $last_state = $entry['NaState'][0]['state'];
                    if($last_state == '1'){
                        $last_state = 'Up';
                    }
                    if($last_state == '0'){
                        $last_state = 'Down';
                    }
                    $state_time = $this->Formatter->diff_in_time($entry['NaState'][0]['created']);
                    $last_state = $last_state." ( $state_time )";
                }
            }

            $connected     = $this->_getConnected($entry['Na']['nasname']);

            //---------------------------------------------------------------------------------------------------------
            //this will return an array containing a 'show' and an 'available_to' key - if show == false will not show
            $return_array       = $this->_getRealmsForNa($na_id);
            //---------------------------------------------------------------------------------------------------------

            if($return_array['show']== true){

                //$quick only updates 'status' and 'connected'
                if($quick){
                    array_push($json_return['items'],array(
                                                        'id'            =>  $entry['Na']['id'],
                                                        'status'        =>  $last_state,
                                                        'connected'     =>  $connected
                    ));
                }else{
                     array_push($json_return['items'],array(
                                                        'id'            =>  $entry['Na']['id'],
                                                        'nasname'       =>  $entry['Na']['nasname'],
                                                        'shortname'     =>  $entry['Na']['shortname'],
                                                        'type'          =>  $entry['Na']['type'],
                                                        'status'        =>  $last_state,
                                                        'connected'     =>  $connected,
                                                        'contact'       =>  $entry['User']['username'],
                                                        'realms'        =>  $return_array['available_to']
                    ));
                }
            }
        }
        //----------------------------------------------------------

        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


     function json_nas_map_index(){   //Only list NAS devices which have lon and lat values
        $this->layout = 'ajax';

        $auth_data = $this->Session->read('AuthInfo');

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok';
        //-----------------------------------------

        $json_return['maps']            = array();

        //Get Sane defaults
        $json_return['maps']['lat']     = -25.74470879185722;
        $json_return['maps']['lon']     =  28.27759087085724;
        $json_return['maps']['type']    = 'G_SATELLITE_MAP';
        $json_return['maps']['zoom']    = 13;
        $json_return['maps']['draggable'] = false;

        //Can this user move things around?
        if($this->Dojolayout->_look_for_right('nas/json_edit')){
            $json_return['maps']['draggable']    = true;
        }


        $qr = $this->Map->findAllByUserId($auth_data['User']['id']);

        foreach($qr as $setting){
            ($setting['Map']['name'] == 'lat'          )&&($json_return['maps']['lat']=$setting['Map']['value']);
            ($setting['Map']['name'] == 'lon'          )&&($json_return['maps']['lon']=$setting['Map']['value']);
            ($setting['Map']['name'] == 'type'         )&&($json_return['maps']['type']=$setting['Map']['value']);
            ($setting['Map']['name'] == 'zoom'         )&&($json_return['maps']['zoom']=$setting['Map']['value']);
        }
        $json_return['maps']['items']   = array();
        //---------------------------------------------------------------------
       
        Configure::load('yfi'); 
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
                $r = $this->Na->find('all',array('conditions' => array('Na.lat <>' => null, 'Na.lon <>' => null))); //Get all the NAS devices without lon and lat defined
        }else{
                $r = $this->Na->find('all',array('conditions' => array('Na.nasname <>' => '127.0.0.1','Na.lat <>' => null, 'Na.lon <>' => null))); //Get all the NAS devices EXCEPT 127.0.0.1
        }

        //Loop through it and check the user's rights decide whether to display or not
        foreach($r as $entry){
            $na_id      = $entry['Na']['id'];
            //---------------------------------------------------------------------------------------------------------
            //this will return an array containing a 'show' and an 'available_to' key - if show == false will not show
            $return_array       = $this->_getRealmsForNa($na_id);
            //---------------------------------------------------------------------------------------------------------
            if($return_array['show']== true){

                $avail = null;
                if($entry['Na']['monitor'] == 1){
                    //Get the last state
                    if(count($entry['NaState']) > 0){
                        $last_state = $entry['NaState'][0]['state'];
                        if($last_state == '1'){
                            $avail = true;
                        }
                        if($last_state == '0'){
                            $avail = false;
                        }
                        $state_time = $this->Formatter->diff_in_time($entry['NaState'][0]['created']);
                        $last_state = $last_state." ( $state_time )";
                    }
                }
                array_push($json_return['maps']['items'],array(
                    'id'            =>  $na_id,
                    'name'          =>  $entry['Na']['shortname']."  (".$entry['Na']['nasname'].")",
                    'lon'           =>  $entry['Na']['lon'],
                    'lat'           =>  $entry['Na']['lat'],
                    'available'     =>  $avail

                ));
            }
        }
        //----------------------------------------------------------
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_nas_map_public(){   //Public view for NAS devices
        $this->layout = 'ajax';

        $auth_data = $this->Session->read('AuthInfo');

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok';
        //-----------------------------------------

        $json_return['maps']            = array();

        //Get Sane defaults
        $json_return['maps']['lat']     = -25.74470879185722;
        $json_return['maps']['lon']     =  28.27759087085724;
        $json_return['maps']['type']    = 'G_SATELLITE_MAP';
        $json_return['maps']['zoom']    = 17;
        $json_return['maps']['draggable'] = false;

        $json_return['maps']['items']   = array();
        //---------------------------------------------------------------------

        $r = $this->Na->find('all',array('conditions' => array('Na.lat <>' => null, 'Na.lon <>' => null))); //Get all the NAS devices without lon and lat defined
        

        //Loop through it and check the user's rights decide whether to display or not
        foreach($r as $entry){
            $na_id      = $entry['Na']['id'];
            if($entry['Na']['monitor'] == 1){
                    //Get the last state
                if(count($entry['NaState']) > 0){
                    $last_state = $entry['NaState'][0]['state'];
                    if($last_state == '1'){
                        $avail = true;
                    }
                    if($last_state == '0'){
                        $avail = false;
                    }
                    $state_time = $this->Formatter->diff_in_time($entry['NaState'][0]['created']);
                    $last_state = $last_state." ( $state_time )";
                }
            }
            array_push($json_return['maps']['items'],array(
                'id'            =>  $na_id,
                'name'          =>  $entry['Na']['shortname']."  (".$entry['Na']['nasname'].")",
                'lon'           =>  $entry['Na']['lon'],
                'lat'           =>  $entry['Na']['lat'],
                'available'     =>  $avail
            ));
        }
        //----------------------------------------------------------
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_nas_map_list(){   //Only list NAS devices which do not have lon and lat values
        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //---------------------------------------------------------------------
        $auth_data = $this->Session->read('AuthInfo');
        Configure::load('yfi'); 
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
                $r = $this->Na->find('all',array('conditions' => array('Na.lat' => null, 'Na.lon' => null))); //Get all the NAS devices without lon and lat defined
        }else{
                $r = $this->Na->find('all',array('conditions' => array('Na.nasname <>' => '127.0.0.1','Na.lat' => null, 'Na.lon' => null))); //Get all the NAS devices EXCEPT 127.0.0.1
        }

        //Loop through it and check the user's rights decide whether to display or not
        $count = 0;
        foreach($r as $entry){
            $na_id      = $entry['Na']['id'];
            //---------------------------------------------------------------------------------------------------------
            //this will return an array containing a 'show' and an 'available_to' key - if show == false will not show
            $return_array       = $this->_getRealmsForNa($na_id);
            //---------------------------------------------------------------------------------------------------------
            if($return_array['show']== true){
                if($count == 0){
                    array_push($json_return['items'],array(
                                                        'id'            =>  $na_id,
                                                        'name'          =>  $entry['Na']['shortname']."  (".$entry['Na']['nasname'].")",
                                                        'selected'      => 'selected'
                    ));
                }else{
                    array_push($json_return['items'],array(
                                                        'id'            =>  $na_id,
                                                        'name'          =>  $entry['Na']['shortname']."  (".$entry['Na']['nasname'].")"
                    ));
                }
                $count ++;
            }
        }
        //----------------------------------------------------------
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_nas_map_move(){
        $this->layout = 'ajax';

        //Can this user do it?
        if(!$this->Dojolayout->_look_for_right('nas/json_edit')){
             $this->set('json_return',$this->Json->permFail());
            return;
        }

        $d              = array();
        $d['Na']['id']  = $this->params['form']['id'];

        $d['Na']['lat'] = null;
        if(array_key_exists('lat',$this->params['form'])){
            $d['Na']['lat'] = $this->params['form']['lat'];
        }

        $d['Na']['lon'] = null;
        if(array_key_exists('lat',$this->params['form'])){
            $d['Na']['lon'] = $this->params['form']['lon'];
        }

        $this->Na->save($d);
        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        //-----------------------------------------

         //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }


    function json_photo_for_nas($nasId){
        $this->layout = 'ajax';
        $q_r = $this->Na->findById($nasId);
        $json_return['json']['status']      = 'ok';
        $json_return['logo']['file_name']    = $q_r['Na']['photo_file_name'];
        $this->set('json_return',$json_return);
    }

    function json_upload_image($nas_id){

        $this->layout = 'ajax';

        $filename   = 'fileToUpload'.$nas_id;
        $file_temp  = $_FILES[$filename]['tmp_name'];
        $name       = $_FILES[$filename]['name'];
        $extension  = $_FILES[$filename]['type'];
        $extension  = preg_replace('/.+\//','',$extension);
        Configure::load('yfi');
        $directory  = Configure::read('realm.icon_directory');

        $filename   = $nas_id.'.'.$extension;
        $new_file   = $directory.$filename;

       // exec("/usr/bin/mogrify -resize x50 $file_temp"); we are not scaling this images!
        exec("cp $file_temp $new_file");
        exec("chmod 644 $new_file");

        $d['Na']['id']                  = $nas_id;
        $d['Na']['photo_file_name']     = $filename;
        $this->Na->save($d);

        $json_return['json']['status']  = 'ok';
        $json_return['image']['file']   = $filename;
        $json_return['image']['name']   = $name;

        $this->set('json_return',$json_return);
    }


     function json_stats(){  //For the dojo Grid
       
        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //__________Dummy Dates___________________________
        //$start_date = '2009-10-01 00:00:00';
        //$end_date   = '2009-10-31 23:59:59';
        $start_date = date ("Y-m-d H:i:s", $this->params['url']['sd']);
        $end_date   = date ("Y-m-d H:i:s", ($this->params['url']['ed']+(60*60*24)-1)); //Go to the end of the day
        //________ END Dummy Dates _______________________


        //---------------------------------------------------------------------
        $auth_data = $this->Session->read('AuthInfo');
        Configure::load('yfi'); 
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
                $r = $this->Na->find('all',array()); //Get all the NAS devices
        }else{
                $r = $this->Na->find('all',array('conditions' => array('Na.nasname <>' => '127.0.0.1'))); //Get all the NAS devices EXCEPT 127.0.0.1
        }


        //Loop through it and check the user's rights decide whether to display or not
        foreach($r as $entry){
            $na_id      = $entry['Na']['id'];
            $na_ip      = $entry['Na']['nasname'];
            //---------------------------------------------------------------------------------------------------------
            //this will return an array containing a 'show' and an 'available_to' key - if show == false will not show
            $return_array       = $this->_getRealmsForNa($na_id);
            //---------------------------------------------------------------------------------------------------------
            if($return_array['show']== true){
                $uptime = $this->_getUptimeForPeriod($na_id,$start_date,$end_date);
                $usage  = $this->_getUsageForPeriod($na_ip, $start_date,$end_date);
               // print_r($usage);
                array_push($json_return['items'],array(
                    'id'            =>  $entry['Na']['id'],
                    'nasname'       =>  $entry['Na']['nasname'],
                    'shortname'     =>  $entry['Na']['shortname'],
                    'uptime'        =>  $uptime,
                    'users'         =>  $usage['users'],
                    'tx'            =>  $usage['total_input'],
                    'rx'            =>  $usage['total_output'],
                    'total'         =>  $usage['total_data']

                ));
            }
        }
        //----------------------------------------------------------

        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


    function json_state($id){


        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'state';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        $q_r = $this->NaState->find('all',array('conditions' =>array('NaState.na_id' => $id)));

        $state_array = array();

        //If there is only one entry - we take it from that entry till now
        if(count($q_r) == 1){
            $state_time = $this->Formatter->diff_in_time($q_r[0]['NaState']['created']);
            array_push($json_return['items'],array('id' =>  0,'state'=>$q_r[0]['NaState']['state'],'time'=> $state_time));
        }

        if(count($q_r) > 1){

            $counter = 0;
            foreach($q_r as $item){
                if($counter != 0){
                    $previous_time  = $q_r[($counter-1)]['NaState']['created'];
                    $previous_state = $q_r[($counter-1)]['NaState']['state'];
                    $id             = $q_r[($counter-1)]['NaState']['id'];
                    $state_time     = $this->Formatter->diff_in_time($q_r[$counter]['NaState']['created'],$previous_time);
                    array_push($json_return['items'],array('id' =>  $id,'state'=>$previous_state,'time'=> $state_time,'start' =>$previous_time,'end' => $q_r[$counter]['NaState']['created'])); 
                }
                $counter++;
            }

            //Add the last one
            $state_now      = $q_r[($counter-1)]['NaState']['state'];
            $state_since    = $q_r[($counter-1)]['NaState']['created'];
            $id             = $q_r[($counter-1)]['NaState']['id'];
            $state_time     = $this->Formatter->diff_in_time($state_since);
            array_push($json_return['items'],array('id' =>  $id,'state'=>$state_now,'time'=> $state_time,'start' => $q_r[($counter-1)]['NaState']['created'])); 
        }

        $result = array_reverse($json_return['items']); //Put the last state at the top!
        $json_return['items'] = $result;       

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_del(){

        $this->layout = 'ajax';

        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $na_id = $this->params['url'][$key];

                //--------------------------------------------
                //See if this is available to all - then 'ap' can not delete it
                $realm_count = $this->NaRealm->find('count',array('conditions' => array('NaRealm.na_id' => $na_id)));

                if($realm_count == 0){
                    $auth_data = $this->Session->read('AuthInfo');
                    Configure::load('yfi');
                    if($auth_data['Group']['name'] == Configure::read('group.ap')){
                       //AP's are NOT deleting something available to '(all)'!!!
                        continue;
                    }
                }
                //-------------------------------------------
                $qr = $this->Na->findById($na_id);
                $nasname = $qr['Na']['nasname'];
                $this->Pptpd->del_nas($nasname);

                $this->Na->del($na_id,true);
                $this->NaRealm->deleteAll(array('NaRealm.na_id' => $na_id));
                $this->NaState->deleteAll(array('NaState.na_id' => $na_id));
                //Clean the VPN if there is an entry - else it will leave the file as is
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_user_list(){

        //The Administrator will get a list of all the administrators and Access Providers
        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //---------------------------------------------------------------------
        Configure::load('yfi');
        $ap_name    = Configure::read('group.ap');
        $admin_name = Configure::read('group.admin');

        $qr = $this->User->find('all',array('conditions'=> array( "or" => array (
                                                                    "Group.name" => array($ap_name,$admin_name)
                                                                    )
                                                                )));
        foreach($qr as $user){

            $user_string = $user['User']['username'];
            array_push($json_return['items'], array('id' => $user['User']['id'],'name' => $user_string));
        }
       // print_r($qr);
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_type_list(){

        //The Administrator will ge a list of all the administrators and Access Providers
        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //---------------------------------------------------------------------
        Configure::load('yfi');
        $types  = Configure::read('nas.device_types'); 
        foreach($types as $type){
            array_push($json_return['items'], array('id' => $type,'name' => $type));
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


    function json_vpn_new(){

        $this->layout = 'ajax';
        $json_return['json']['status']    = 'ok';
        $json_return['nas'] = $this->Pptpd->show_next_nas();

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }


    function json_add(){

        $this->layout = 'ajax';

        $d = array();

        //Check if it is a VPN or a Normal
        if(array_key_exists('nasname',$this->params['form'])){  //Normal

            $d['Na']['nasname']    = $this->params['form']['nasname'];
        }

        if(array_key_exists('vpn_nasname',$this->params['form'])){  //VPN
            
            $next_entry =   $this->Pptpd->add_next_nas();
            $d['Na']['nasname']    = $next_entry['ip'];
            //Add the VPN client to the chap secrets file
        }
        
        $d['Na']['id']         = '';
        $d['Na']['shortname']  = $this->params['form']['shortname'];
        $d['Na']['secret']     = $this->params['form']['secret'];
        $d['Na']['user_id']    = $this->params['form']['user_id'];
        $this->Na->save($d);

        $json_return= array();
        $json_return['nas']['id']    = $this->Na->id;

        if(array_key_exists('available_all',$this->params['form'])){

            //Available to all does not add any na_realm entries

        }else{

            foreach(array_keys($this->params['url']) as $key){
                if(preg_match('/^\d+/',$key)){
                    //----------------
                    $realm_id = $this->params['url'][$key];
                    $this->_add_nas_realm($json_return['nas']['id'],$realm_id);
                    //-------------
                }
            }
        }
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_view($id=null){

        $this->layout = 'ajax';

        $qr = $this->Na->findById($id);
        $nasname = $qr['Na']['nasname'];
        //Check if this is a VPN client
        $vpn_detail = $this->Pptpd->show_nas($nasname);
        if($vpn_detail != ''){
            $json_return['Na']['vpn_nasname'] = $nasname;
        }
        $json_return['Na']['nasname']   = $nasname;
        $json_return['Na']['shortname'] = $qr['Na']['shortname'];
        $json_return['Na']['secret']    = $qr['Na']['secret'];
        $json_return['Na']['user_id']   = $qr['Na']['user_id'];

        //Check if it is asigned only to certain realms
        $json_return['Na']['available_to_all']   = false;
        if(count($qr['NaRealm'])< 1){
            $json_return['Na']['available_to_all']   = true;
        }
       // print_r($qr);

        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);

    }

    function json_map_view($id=null){


        $this->layout = 'ajax';

        $qr = $this->Na->findById($id);
        //print_r($qr);
        $nasname = $qr['Na']['nasname'];
        //Check if this is a VPN client

        $json_return['Na']['nasname']       = $nasname;
        $json_return['Na']['shortname']     = $qr['Na']['shortname'];
        $json_return['Na']['description']   = $qr['Na']['description'];
        $json_return['Na']['photo_file_name'] = $qr['Na']['photo_file_name'];

        $last_state = 'No Data';

        if($qr['Na']['monitor'] == 1){
            //Get the last state
            if(count($qr['NaState']) > 0){
                $last_state = $qr['NaState'][0]['state'];
                if($last_state == '1'){
                    $last_state = 'Up';
                }
                if($last_state == '0'){
                    $last_state = 'Down';
                }
                $state_time = $this->Formatter->diff_in_time($qr['NaState'][0]['created']);
                $last_state = $last_state." ( $state_time )";
            }
        }
        $json_return['Na']['status']     = $last_state;
        $connected                      = $this->_getConnected($nasname);
        $json_return['Na']['connected'] = $connected;
      
        //Check if it is asigned only to certain realms
        $json_return['Na']['available_to_all']   = false;
        if(count($qr['NaRealm'])< 1){
            $json_return['Na']['available_to_all']   = true;
        }
       // print_r($qr);

        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }


    function json_realms_for_nas($naId){

         $this->layout = 'ajax';

        /*
        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------
        */

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok';
        $json_return['items']              = array();
        //-----------------------------------------

        //-- Query the DB ---------------------------------------------
        $r_all = $this->Realm->find(
                            'all',
                            array(
                                'conditions'=> $this->Rights->GetRealmClause(),
                                'fields'=>array('Realm.name', 'Realm.id'),
                                'order' => 'Realm.name ASC',
                            )
            );


        foreach($r_all as $entry){

            //Check if there is an entry in the UserRealm table for this user
            $count = $this->NaRealm->find('count', array('conditions' => array('NaRealm.na_id' => $naId,'NaRealm.realm_id' => $entry['Realm']['id'])));
            if($count){
                array_push($json_return['items'],array('name' => $entry['Realm']['name'], 'id' => $entry['Realm']['id'], 'selected' => 'selected')); //Select the first one
            }else{
                array_push($json_return['items'],array('name' => $entry['Realm']['name'], 'id' => $entry['Realm']['id'], 'selected' => ''));
            }
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }


    function json_view_optional($id=null){

        $this->layout = 'ajax';

        $qr = $this->Na->findById($id);
        $nasname = $qr['Na']['nasname'];
        //Check if this is a VPN client
        $vpn_detail = $this->Pptpd->show_nas($nasname);
        if($vpn_detail != ''){
            $json_return['Na']['vpn_nasname']       = $vpn_detail['ip'];
            $json_return['Na']['vpn_user']          = $vpn_detail['client'];
            $json_return['Na']['vpn_password']      = $vpn_detail['secret'];
            $json_return['Na']['vpn_server_name']   = $vpn_detail['server'];
            $json_return['Na']['vpn_server_ip']     = $vpn_detail['server_ip'];
        }
        $json_return['Na']['type']          = $qr['Na']['type'];
        $json_return['Na']['ports']         = $qr['Na']['ports'];
        $json_return['Na']['community']     = $qr['Na']['community'];
        $json_return['Na']['description']   = $qr['Na']['description'];
        $json_return['Na']['monitor']       = $qr['Na']['monitor'];
        $json_return['Na']['lat']           = $qr['Na']['lat'];
        $json_return['Na']['lon']           = $qr['Na']['lon'];

        //Check if it is asigned only to certain realms
        $json_return['Na']['available_to_all']   = false;
        if(count($qr['NaRealm'])< 1){
            $json_return['Na']['available_to_all']   = true;
        }
       // print_r($qr);

        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);

    }

   
    function json_edit(){

        $this->layout = 'ajax';

        $nas_id                 = $this->params['form']['id'];

        $d = array();
        $d['Na']['id']          = $nas_id;
        if(array_key_exists('nasname',$this->params['form'])){
            $d['Na']['nasname'] = $this->params['form']['nasname'];
        }
        $d['Na']['shortname']   = $this->params['form']['shortname'];
        $d['Na']['secret']      = $this->params['form']['secret'];
        $d['Na']['user_id']     = $this->params['form']['user_id'];
        $this->Na->save($d);    //Updated

        //Check if we need to remove any realms
        if(array_key_exists('available_all',$this->params['form'])){
            //Remove any existing NaRealm bindings
            $this->NaRealm->deleteAll(array('NaRealm.na_id'=> $nas_id));

        }else{

            //Remove any existing NaRealm bindings
            $this->NaRealm->deleteAll(array('NaRealm.na_id'=> $nas_id));
            //Get the list of realms passed to us
            foreach(array_keys($this->params['url']) as $key){
                if(preg_match('/^\d+/',$key)){
                    //----------------
                    $realm_id = $this->params['url'][$key];
                    $this->_add_nas_realm($nas_id,$realm_id);
                    //-------------
                }
            }
        }
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_edit_optional(){

        $this->layout = 'ajax';
        $nas_id                 = $this->params['form']['id'];

        $d = array();
        $d['Na']['id']          = $nas_id;
        if(array_key_exists('monitor',$this->params['form'])){
            $d['Na']['monitor'] = 1;
        }else{
            $d['Na']['monitor'] = 0;
        }

        //This is an enhancement to work through NATed connections
        //It requires that the communtiy is a valid MAC address in the form 08-00-27-83-EB-FB and that active monitoring is set
        $json_return['json'] = array();
        
        //Clear the heartbeat table from this entry
        $this->Heartbeat->deleteAll(array('Heartbeat.na_id' => $nas_id));

        if($this->params['form']['type'] == 'CoovaChilli-NAT'){
            $subject = $this->params['form']['community'];
            $pattern = '/^([0-9a-fA-F]{2}[-]){5}[0-9a-fA-F]{2}$/i';
            if(preg_match($pattern, $subject)< 1){
                $json_return['json']['status'] = "error";
                $json_return['json']['detail'] = "Missing or wrong format for MAC Address (Community's value used in conjunction with CoovaChilli-NAT)";
            }else{
                //We also need to force active monitoring for NATed connections using heartbeat;
                if($d['Na']['monitor'] == 0){
                    $json_return['json']['status'] = "error";
                    $json_return['json']['detail'] = "Active monitor required for CoovaChilli-NAT";
                }else{
                    //Ensure that if active monitor is set and that we also write an entry into the heartbeats table (if it does not exist already!)
                    $count = $this->Heartbeat->find('count',array( 'conditions' => array('Heartbeat.na_id' => $nas_id)));
                    if($count == 0){
                        $hb['Heartbeat']['na_id'] = $nas_id;
                        $this->Heartbeat->save($hb);
                    }
                }
            }
        }

        //Test if there was no failure, then save
        if(!array_key_exists('status',$json_return['json'])){    
            $d['Na']['type']        = $this->params['form']['type'];
            $d['Na']['ports']       = $this->params['form']['ports'];
            $d['Na']['community']   = $this->params['form']['community'];
            $d['Na']['description'] = $this->params['form']['description'];
            $d['Na']['lon']         = $this->params['form']['lon'];
            $d['Na']['lat']         = $this->params['form']['lat'];
            $this->Na->save($d);    //Updated
            $json_return['json']['status'] = "ok";
        }

        $this->set('json_return',$json_return);
    }


    function json_restart_chk(){

        $this->layout = 'ajax';


        // Check the back-off interval
        Configure::load('yfi');
        $back_off = Configure::read('freeradius.back_off_minutes');

        $q_r =$this->Check->find('first',array('conditions' => array('Check.name' =>'radius_restart')));
        $restarted;

        if($q_r){

            $restarted   = $q_r['Check']['modified'];

        }else{

            //Not yet restarted VIA CRON script
            $json_return['restart_wait']    = true;
            $json_return['json']['status']  = "ok";
            $this->set('json_return',$json_return);
            return;
        }


        //Get a list of NAS devices for this user
        $r = $this->Na->find('all',array()); //Get all the NAS devices
        //Loop through it and check the user's rights decide whether to display or not
        $restart_flag = false;
        foreach($r as $entry){

            //---------------------------------------------------------------------------------------------------------
            //this will return an array containing a 'show' and an 'available_to' key - if show == false will not show
            $return_array       = $this->_getRealmsForNa($entry['Na']['id']);
            //---------------------------------------------------------------------------------------------------------

            if($return_array['show']== true){
                //Check against the last restart date
                $modified   = $entry['Na']['modified'];
                if(strtotime($restarted) < strtotime($modified)){
                    $restart_flag = true;
                    //------------------------------------------
                    $last_plus_cool_off = strtotime($restarted)+ ($back_off * 60);
                    $dateTime       = new DateTime("now");
                    $date_now       = $dateTime->format("Y-m-d H:i:s"); 
                    $now            = strtotime($date_now);
                    $clear          = $now-$last_plus_cool_off;
                    if($clear > 0){

                        //---Use a session variable to count down---
                        if($this->Session->check('Nas.restart')){
                            $initial = $this->Session->read('Nas.restart');
                            $json_return['restart_countdown']    = $this->Formatter->_sec2hms(abs($initial - $now));
                        }else{
                            $this->Session->write('Nas.restart',($now+300));
                            $json_return['restart_countdown']    = $this->Formatter->_sec2hms(300);    //Start with 5 minutes
                        }
                        //----------------------------------------

                    }else{
                        $time = $this->Formatter->_sec2hms(abs($clear));
                        $json_return['restart_countdown']    = $time;       //Count down for cron
                    }

                    //-----------------------------------------
                    break;
                }
            }
        }

        $json_return['restart_wait']    = $restart_flag;
        $json_return['json']['status']  = "ok";
        $this->set('json_return',$json_return);

    }

    function json_delete_avail(){

        $this->layout = 'ajax';

        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $state_id = $this->params['url'][$key];
                $this->NaState->del($state_id,true);
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
        //--Description: Get the actions to display on templates tab----------------- 
        //--AP Sepcific: YES --------------------------------------------------------
        //--Rights Completed:   NA -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                     = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_nas();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_actions_view(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a NAS view Availabiltiy tab----
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                       = 'ajax';
        $json_return['items']               = $this->Dojolayout->actions_for_nas_view();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    //========================================================================================================================
    //===================== Private Functions ================================================================================
    //========================================================================================================================
     function _add_nas_realm($nas_id,$realm_id){

        $d                          = array();
        $d['NaRealm']['id']         = '';
        $d['NaRealm']['na_id']      = $nas_id;
        $d['NaRealm']['realm_id']   = $realm_id;
        $this->NaRealm->save($d);
        $this->NaRealm->id          = false;
    }


    function _getConnected($nas_ip){

        $counter = $this->Radacct->find('count',array('conditions' => array('Radacct.nasipaddress' => $nas_ip,'Radacct.acctstoptime'=>null)));
        return $counter;
    }


    function _getRealmsForNa($na_id){
    //Determine the realms for a NAS device and if the current user (AccessProvider) has rights to view it

        $qr =$this->NaRealm->find('all',array('conditions' => array('NaRealm.na_id' => $na_id)));

        $realms_string ='(all)';        //Default if none are defined
        $show_flag = true;

        $count = 0;
        $loop_trigger = false;
        foreach($qr as $item){

            $this_realm     = $item['Realm']['name'];
            $realm_id       = $item['Realm']['id'];
            if($this->Rights->CheckRealmIdAllowed($realm_id)){    
                if($count > 0){
                    $realms_string   = $realms_string.'<br> '.$this_realm;
                }else{
                    $realms_string = $this_realm;
                }
                $count++;
            }
            $loop_trigger = true;   //There was realms tied to this template
        }

        if(($loop_trigger)&&($realms_string == '(all)')){   //Check it there were realms tied to this and whether all were rejected 

            $auth_data = $this->Session->read('AuthInfo');
            Configure::load('yfi'); 
            if($auth_data['Group']['name'] == Configure::read('group.admin')){
                $show_flag = true;
            }else{
                $show_flag = false;
            }
        }

        $ret_arr = array('show'=>$show_flag,'available_to' =>$realms_string);
        return $ret_arr;
    }

    function _getUptimeForPeriod($na_id,$start_date,$end_date){

        $start_state= 0; //Start the unit as not available;

        //Get the state just before the start date
        $q_r = $this->NaState->find('first',array('conditions' => array('NaState.na_id' => $na_id,'NaState.created <' => $start_date),'order' => 'NaState.created DESC'));

        if($q_r != ''){
            $start_state = $q_r['NaState']['state'];        //Change the state is neccesary
        }

        //Get the states between the start of stats query and any transitionals
        $q_r = $this->NaState->find('all',array('conditions' => array('NaState.na_id' => $na_id,'NaState.created <=' => $end_date,'NaState.created >=' => $start_date)));

        $start_time = $start_date;
        $state      = $start_state;
        $uptime     = 0;
        $downtime   = 0;

        foreach($q_r as $transition){

            $time_in_state = abs(strtotime($transition['NaState']['created'])-strtotime($start_time));
            if($state == 1){
                $uptime     = $uptime + $time_in_state;
            }

            if($state == 0){
                $downtime   = $uptime + $time_in_state;
            }
                
           // print_r("====== Downtime : ".$downtime." ===== Uptime".$uptime."====\n\n");
            // print_r($time_in_state);
            $start_time = $transition['NaState']['created'];
            $state      = $transition['NaState']['state'];

        }

        //---------LAST ENTRY-----------------------
        //From the last recording untill the next one the state swapped
        if($start_time != $start_date){ //It must have looped
            $time_in_state =  abs(strtotime($end_date) - strtotime($start_time));
            if($state == 0){
                $uptime     = $uptime + $time_in_state; 
            }
            if($state == 1){
                $downtime   = $uptime + $time_in_state;
            }
        }else{      //No looping 
            if($start_state == 1){
                $uptime = abs(strtotime($end_date) - strtotime($start_date));
            }else{
                $downtime = abs(strtotime($end_date) - strtotime($start_date));
            }
        }
        //------- LAST ENTRY------------------------

        //$uptime = ($downtime / $uptime) * 100;
        $pers_val = 100;
        if($downtime != 0){
            $pers_val = ($uptime /(abs(strtotime($end_date) - strtotime($start_date)))) * 100;
            $pers_val =round($pers_val,2);
        }
        if($uptime == 0){

            $pers_val = 0;
        }

        //return $pers_val.'% (Up: '.$this->Formatter->formatted_seconds($uptime).' Down: '.$this->Formatter->formatted_seconds($downtime).')';
        return $pers_val.'%';

    }

    function _getUsageForPeriod($na_ip,$start_date,$end_date){

        $query = "SELECT SUM(acctinputoctets) AS total_input,SUM(acctoutputoctets) AS total_output, SUM(acctsessiontime) as total_time FROM radacct AS Radacct where nasipaddress='".$na_ip."' AND acctstarttime >='".$start_date."' AND acctstoptime <='".$end_date."'";

        $qr = $this->Radacct->query($query);
        $total_input = $total_output = $total_time = 0;
        //print_r($qr);
        ($qr[0][0]['total_input']   != '')  &&  ($total_input    = $qr[0][0]['total_input']);
        ($qr[0][0]['total_output']  != '')  &&  ($total_output   = $qr[0][0]['total_output']);
        ($qr[0][0]['total_time']    != '')  &&  ($total_time     = $qr[0][0]['total_time']);

        $query = "SELECT COUNT(DISTINCT username) AS users FROM radacct AS Radacct where nasipaddress='".$na_ip."' AND acctstarttime >='".$start_date."' AND acctstoptime <='".$end_date."'";
        $qr = $this->Radacct->query($query);

        $ra             = array();
        $ra['total_input'] = $total_input;
        $ra['total_output']= $total_output;
        $ra['total_time']  = $total_time;
        $ra['total_data']  = $total_input + $total_output;
        $ra['users']       = $qr[0][0]['users'];

        return $ra;

    }


}
?>
