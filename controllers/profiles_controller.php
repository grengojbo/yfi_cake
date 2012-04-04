<?
class ProfilesController extends AppController {
    var $name       = 'Profiles';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component
    var $uses       = array('Profile','Template','ProfileRealm','Realm','Radgroupcheck','Radgroupreply','TemplateAttribute','Voucher','Radusergroup','User');

   // var $scaffold;

     function json_index(){  //For the dojo Grid
         //----------------------------------------------------------------------------
        //--Description: Grid format list profiles ------------------------------------
        //--AP Sepcific: Only list profiles assigned to all or realms of AP    --------
        //--Rights Completed:    17-3-9 ----------------------------------------------
        //----------------------------------------------------------------------------

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
        $r = $this->Profile->find(
                            'all',
                            array()
            );
        foreach($r as $entry){

            $attribute_count    = $this->_getAttributeCount($entry['Profile']['name']);

            //---------------------------------------------------------------------------------------------------------
            //this will return an array containing a 'show' and an 'available_to' key - if show == false will not show
            $return_array       = $this->_getRealmsForProfile($entry['Profile']['id']);
            //---------------------------------------------------------------------------------------------------------

            if($return_array['show']== true){

                array_push( $json_return['items'],
                            array(  'id'            => $entry['Profile']['id'],
                                    'name'          => $entry['Profile']['name'],
                                    'template'      => $entry['Template']['name'],
                                    'available_to'  => $return_array['available_to'], 
                                    'reply_attribute_count' => $attribute_count['reply_count'], 
                                    'check_attribute_count' => $attribute_count['check_count']
                            )
                );

            } 
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


    function json_list_for_user($user_id){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        $auth_data  = $this->Session->read('AuthInfo');
        //print_r($auth_data);

        //We need to determine the realm id for this user in order to determine if a profile is assigned to a realm
        $realm_suffix   = $auth_data['User']['username'];
        $realm_suffix   = preg_replace("/^.+@/","",$realm_suffix);
        $q_r            = $this->Realm->find('first',array('conditions' => array('Realm.append_string_to_user' => $realm_suffix)));
        $realm_id       = $q_r['Realm']['id'];

        if($user_id == $auth_data['User']['id']){

            $q_r = $this->Profile->find('all',array());

           foreach($q_r as $profile){

                if(count($profile['ProfileRealm'])== 0){

                    array_push($json_return['items'], array('id' => $profile['Profile']['id'], 'name' => $profile['Profile']['name']));

                }else{
                    foreach($profile['ProfileRealm'] as $p_r){
                            if($p_r['realm_id'] == $realm_id){
                                array_push($json_return['items'], array('id' => $profile['Profile']['id'], 'name' => $profile['Profile']['name']));
                                break;
                            }
                    }

                }
            }
        }

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }

    function json_add(){

        //----------------------------------------------------------------------------
        //--Description: Add profiles each profile can be ----------------------------
        //---------------available to all or certain realms --------------------------
        //--AP Sepcific: NOT (taken care of in the Form) -----------------------------
        //--Rights Completed:    17-3-9 ----------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------


        $d = array();
        $d['Profile']['name']  = $this->params['form']['name'];
        $d['Profile']['template_id']  = $this->params['form']['template'];

        $this->Profile->save($d);

        $json_return= array();
        $json_return['profile']['id']    = $this->Profile->id;

        //Get the template name
        $qr = $this->Template->findById($this->params['form']['template']);
        $template_name = $qr['Template']['name'];

        $attribute_count = $this->_addAttributesFromTemplate($this->params['form']['name'],$this->params['form']['template']);


        if(array_key_exists('available_all',$this->params['form'])){

            //If all is selected we will not add any template_realms entries
            $json_return['profile']['available_to'] = '(all)';

        }else{

            $template_realm = '';
            $count = 0;
            foreach(array_keys($this->params['url']) as $key){
                if(preg_match('/^\d+/',$key)){
                    //----------------
                    $realm_id = $this->params['url'][$key];     //Add a template_realm for each realm selected
                    if($count > 0){
                        $template_realm   = $template_realm.'<br> '.$this->_add_profile_realm($this->Profile->id,$realm_id);
                    }else{
                        $template_realm = $this->_add_profile_realm($this->Profile->id,$realm_id);
                    }
                    $count++;
                    //-------------
                }
            }
            $json_return['profile']['available_to'] = $template_realm;
        }

        $json_return['profile']['name']                     = $this->params['form']['name'];
        $json_return['profile']['template']                 = $template_name;
        $json_return['profile']['reply_attribute_count']    = $attribute_count['reply_count'];
        $json_return['profile']['check_attribute_count']    = $attribute_count['check_count'];
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

     function json_del(){

        //----------------------------------------------------------------------------
        //--Description: Delete Profiles --------------------------------------------
        //--AP Sepcific: AP's can not delete '(all)' Profiles (since only admin can create them)
        //--Rights Completed:    17-3-9 ----------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';

        //--------Check the rights-------------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $profile_id = $this->params['url'][$key];

                //--------------------------------------------
                //See if this is available to all - then 'ap' can not delete it
                $realm_count = $this->ProfileRealm->find('count',array('conditions' => array('ProfileRealm.profile_id' => $profile_id)));

                if($realm_count == 0){
                    $auth_data = $this->Session->read('AuthInfo');
                    Configure::load('yfi');
                    if($auth_data['Group']['name'] == Configure::read('group.ap')){
                       //AP's are NOT deleting something available to '(all)'!!!
                        continue;
                    }
                }
                //-------------------------------------------



                //-----Profile Cleanup Bit---------
                //Get the profile's name
                $qr = $this->Profile->findById($profile_id);
                $profile_name = $qr['Profile']['name'];
                $this->Radgroupcheck->deleteAll(array('Radgroupcheck.groupname' => $profile_name));
                $this->Radgroupreply->deleteAll(array('Radgroupreply.groupname' => $profile_name));
                $this->ProfileRealm->deleteAll(array('ProfileRealm.profile_id'  => $profile_id));
                //---END Profile Cleanup Bit -----

                $this->Profile->del($profile_id,true);
           
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_edit($id){

        //----------------------------------------------------------------------------
        //--Description: List Profile Attributes for specified profile (dojo grid)---
        //--AP Sepcific: TODO --------------------------------------------------------
        //--Rights Completed:   TODO -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        //Get the profile name for this id
        $qr = $this->Profile->findById($id);
        $profile_name   = $qr['Profile']['name'];
        $template_id    = $qr['Profile']['template_id'];

        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupcheck->find('all',array('conditions' => array('Radgroupcheck.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupcheck']['attribute'];
            $id         = 'check_'.$item['Radgroupcheck']['id'];
            $op         = $item['Radgroupcheck']['op'];
            $tt_unit    = $this->_getToolTipAndUnit($attribute,$template_id);
            $value      = $item['Radgroupcheck']['value'];
            array_push($json_return['items'],array('id' => $id, 'name' => $attribute,'type'=> 'Check' ,'op' => $op,'tt' => $tt_unit['tooltip'], 'value' => $value,'unit' => $tt_unit['unit']));
        }

        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupreply->find('all',array('conditions' => array('Radgroupreply.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupreply']['attribute'];
            $id         = 'reply_'.$item['Radgroupreply']['id'];
            $op         = $item['Radgroupreply']['op'];
            $tt_unit    = $this->_getToolTipAndUnit($attribute,$template_id);
            $value      = $item['Radgroupreply']['value'];
            array_push($json_return['items'],array('id' => $id, 'name' => $attribute,'type'=> 'Reply' ,'op' => $op,'tt' => $tt_unit['tooltip'], 'value' => $value,'unit' => $tt_unit['unit']));
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


    function json_view_for_voucher($id){

        //-----------------------------------------------------------------------------
        //--Description: List Profile Attributes for specified profilename (dojo grid)-
        //--AP Sepcific: TODO ---------------------------------------------------------
        //--Rights Completed:   TODO --------------------------------------------------
        //-----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();


        //Find the voucher's group
        $qr = $this->Voucher->findById($id);
       // print_r($qr);
        $username       = $qr['Radcheck']['username'];
        $qr             = $this->Radusergroup->findByUsername($username);
        $profile_name   = $qr['Radusergroup']['groupname'];


        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupcheck->find('all',array('conditions' => array('Radgroupcheck.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupcheck']['attribute'];
            $id         = 'check_'.$item['Radgroupcheck']['id'];
            $op         = $item['Radgroupcheck']['op'];
            $value      = $item['Radgroupcheck']['value'];
            array_push($json_return['items'],array('id' => $id, 'profile'=> $profile_name, 'name' => $attribute,'type'=> 'Check' ,'op' => $op, 'value' => $value));
        }

        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupreply->find('all',array('conditions' => array('Radgroupreply.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupreply']['attribute'];
            $id         = 'reply_'.$item['Radgroupreply']['id'];
            $op         = $item['Radgroupreply']['op'];
            $value      = $item['Radgroupreply']['value'];
            array_push($json_return['items'],array('id' => $id,'profile'=> $profile_name, 'name' => $attribute,'type'=> 'Reply' ,'op' => $op, 'value' => $value));
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_view_for_user($id){

        //-----------------------------------------------------------------------------
        //--Description: List Profile Attributes for specified profilename (dojo grid)-
        //--AP Sepcific: TODO ---------------------------------------------------------
        //--Rights Completed:   TODO --------------------------------------------------
        //-----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();


        //Find the voucher's group
        $qr = $this->User->findById($id);
        // print_r($qr);
        $username       = $qr['User']['username'];
        $qr             = $this->Radusergroup->findByUsername($username);
        $profile_name   = $qr['Radusergroup']['groupname'];


        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupcheck->find('all',array('conditions' => array('Radgroupcheck.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupcheck']['attribute'];
            $id         = 'check_'.$item['Radgroupcheck']['id'];
            $op         = $item['Radgroupcheck']['op'];
            $value      = $item['Radgroupcheck']['value'];
            array_push($json_return['items'],array('id' => $id, 'profile'=> $profile_name, 'name' => $attribute,'type'=> 'Check' ,'op' => $op, 'value' => $value));
        }

        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupreply->find('all',array('conditions' => array('Radgroupreply.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupreply']['attribute'];
            $id         = 'reply_'.$item['Radgroupreply']['id'];
            $op         = $item['Radgroupreply']['op'];
            $value      = $item['Radgroupreply']['value'];
            array_push($json_return['items'],array('id' => $id,'profile'=> $profile_name, 'name' => $attribute,'type'=> 'Reply' ,'op' => $op, 'value' => $value));
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }



    function json_attribute_change(){

		$item_id 	=  $this->params['url']['id'];
		$field		=  $this->params['url']['column'];
		$new_value  =  $this->params['url']['new_value'];

        $this->layout = 'ajax';

        //Determine which radgroup<check|reply> table to select
        $pieces = explode('_',$item_id);

        if($pieces[0] == 'check'){

            $d['Radgroupcheck']['id']       = $pieces[1];
            $d['Radgroupcheck'][$field]     = $new_value;
            $this->Radgroupcheck->save($d);
        }

        
        if($pieces[0] == 'reply'){

            $d['Radgroupreply']['id']       = $pieces[1];
            $d['Radgroupreply'][$field]     = $new_value;
            $this->Radgroupreply->save($d);
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

     function json_attribute_delete(){

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
                    $this->Radgroupcheck->del($pieces[1], true);
                }

                if($pieces[0] == 'reply'){
                    $this->Radgroupreply->del($pieces[1], true);
                }
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

        $json_return['items']             = $this->Dojolayout->actions_for_profiles();
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
        $json_return['items']             = $this->Dojolayout->actions_for_profile_view();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


    //========================================================================================================================
    //===================== Private Functions ================================================================================
    //========================================================================================================================

    function _getToolTipAndUnit($attribute,$template_id){

        $qr     = $this->TemplateAttribute->find('first',array('conditions' => array('TemplateAttribute.template_id' => $template_id, 'TemplateAttribute.attribute' => $attribute)));
        $tt     = $qr['TemplateAttribute']['tooltip'];
        $unit   = $qr['TemplateAttribute']['unit'];
        return array('tooltip'=>$tt, 'unit'=> $unit);

    }


    function _getAttributeCount($profile_name){

        $check_count = $this->Radgroupcheck->find('count', array('conditions' => array('Radgroupcheck.groupname' => $profile_name)));
        $reply_count = $this->Radgroupreply->find('count', array('conditions' => array('Radgroupreply.groupname' => $profile_name)));
        return array('check_count' => $check_count, 'reply_count' => $reply_count);

    }

    function _getRealmsForProfile($profile_id){

        $qr =$this->ProfileRealm->find('all',array('conditions' => array('ProfileRealm.profile_id' => $profile_id)));

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

     function _add_profile_realm($profile_id,$realm_id){

        $d = array();
        $d['ProfileRealm']['id']               = '';
        $d['ProfileRealm']['profile_id']       = $profile_id;
        $d['ProfileRealm']['realm_id']         = $realm_id;

        $this->ProfileRealm->save($d);
        //Get the Realm Name of the RealmID
        $qr = $this->Realm->findById($realm_id);
        $realm_name = $qr['Realm']['name'];
        return $realm_name;
    }

    function _addAttributesFromTemplate($profile_name,$template_id){

        $reply_count = 0;
        $check_count = 0;

        //Get the attributes associated with this template
        $qr = $this->TemplateAttribute->find('all',array('conditions' => array('TemplateAttribute.template_id' => $template_id)));

        foreach($qr as $item){

            $attribute  = $item['TemplateAttribute']['attribute'];
            $type       = $item['TemplateAttribute']['type'];

            if($type == 'Check'){

                $d['Radgroupcheck']['groupname'] = $profile_name;
                $d['Radgroupcheck']['attribute'] = $attribute;
                $d['Radgroupcheck']['value']     = 'Replace this value';
                $this->Radgroupcheck->save($d);
                $this->Radgroupcheck->id        = '';   //Clear the id
                $check_count++;
            }

            if($type == 'Reply'){

                $d['Radgroupreply']['groupname'] = $profile_name;
                $d['Radgroupreply']['attribute'] = $attribute;
                $d['Radgroupreply']['value']     = 'Replace this value';
                $this->Radgroupreply->save($d);
                $this->Radgroupreply->id         = '';   //Clear the id
                $reply_count++;
            }
        }

        return array('check_count' => $check_count, 'reply_count' => $reply_count);
    }

}
?>