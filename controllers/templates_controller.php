<?
class TemplatesController extends AppController {
    var $name       = 'Templates';
    var $helpers    = array('Javascript');

    var $components = array('Session','Freeradius','Rights','Json','Dojolayout');    //Add the locker component
    var $uses       = array('Template','TemplateAttribute','TemplateRealm','Realm');

    //var $scaffold;


    function json_index(){  //For the dojo Grid
        //----------------------------------------------------------------------------
        //--Description: Grid format list templates ----------------------------------
        //--AP Sepcific: Only list templates assigned to all or realms of AP    ------
        //--Rights Completed:    16-3-9 ----------------------------------------------
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

        //---------------------------------------------------------------------
        $r = $this->Template->find('all',array()); //Get all the templates

        //Loop through it and check the user's rights decide whether to display or not
        foreach($r as $entry){

            $attribute_count    = $this->_getAttributeCount($entry['TemplateAttribute']);

            //---------------------------------------------------------------------------------------------------------
            //this will return an array containing a 'show' and an 'available_to' key - if show == false will not show
            $return_array       = $this->_getRealmsForTemplate($entry['Template']['id']);
            //---------------------------------------------------------------------------------------------------------

            if($return_array['show']== true){
                array_push($json_return['items'],array(
                                                        'id'            =>$entry['Template']['id'],
                                                        'name'          =>$entry['Template']['name'],
                                                        'available_to'  => $return_array['available_to'],
                                                        'reply_attribute_count' => $attribute_count['reply_count'], 
                                                        'check_attribute_count' => $attribute_count['check_count']
                ));
            }
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


    function json_index_list(){    //For the dojo List

        //----------------------------------------------------------------------------
        //--Description: Form control format list templates (ONLY with attributes) ---
        //--AP Sepcific: Only list templates assigned to all or realms of AP    ------
        //--Rights Completed:    16-3-9 ----------------------------------------------
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
        $r = $this->Template->find(
                            'all',
                            array(
                                'fields'=>array('Template.name', 'Template.id'),
                                'order' => 'Template.name ASC',
                            )
            );

        $count = 0;
        foreach($r as $entry){
            
            //--------------------------------------------------------------------
            //We do not list templates without attributes (They are not useable in profiles)
            $attr_count = $this->TemplateAttribute->find('count',array('conditions' => array('TemplateAttribute.template_id' => $entry['Template']['id'])));
            if($attr_count == 0){
                continue;
            }
            //-----------------------------------------------------------------

            //----------------------------------------------------------------
            $return_array       = $this->_getRealmsForTemplate($entry['Template']['id']);
            if($return_array['show']== true){
                if($count == 0){
                    array_push($json_return['items'],array('name' => $entry['Template']['name'], 'id' => $entry['Template']['id'], 'selected' => 'selected')); //Select the first one
                }else{
                    array_push($json_return['items'],array('name' => $entry['Template']['name'], 'id' => $entry['Template']['id'], 'selected' => ''));
                }
                $count ++;
            }
            //--------------------------------------------------------------------
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


    function json_add(){

        //----------------------------------------------------------------------------
        //--Description: Add templates each template can be --------------------------
        //---------------available to all or certain realms --------------------------
        //--AP Sepcific: NOT (taken care of in the Form ------------------------------
        //--Rights Completed:    16-3-9 ----------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------


        $d = array();
        $d['Template']['name']  = $this->params['form']['name'];

        $this->Template->save($d);

        $json_return= array();
        $json_return['template']['id']    = $this->Template->id;


        if(array_key_exists('available_all',$this->params['form'])){

            //If all is selected we will not add any template_realms entries
            $json_return['template']['available_to'] = '(all)';
            

        }else{

            $template_realm = '';
            $count = 0;
            foreach(array_keys($this->params['url']) as $key){
                if(preg_match('/^\d+/',$key)){
                    //----------------
                    $realm_id = $this->params['url'][$key];     //Add a template_realm for each realm selected
                    if($count > 0){
                        $template_realm   = $template_realm.'<br> '.$this->_add_template_realm($this->Template->id,$realm_id);
                    }else{
                        $template_realm = $this->_add_template_realm($this->Template->id,$realm_id);
                    }
                    $count++;
                    //-------------
                }
            }
            $json_return['template']['available_to'] = $template_realm;
        }

        $json_return['template']['name'] = $this->params['form']['name'];
        $json_return['template']['reply_attribute_count'] = 0;
        $json_return['template']['check_attribute_count'] = 0;
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_del(){

        //----------------------------------------------------------------------------
        //--Description: Delete Templates --------------------------------------------
        //--AP Sepcific: AP's can not delete '(all)' Templates (since only admin can create them)
        //--Rights Completed:    16-3-9 ----------------------------------------------
        //----------------------------------------------------------------------------

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
                $template_id = $this->params['url'][$key];

                //--------------------------------------------
                //See if this is available to all - then 'ap' can not delete it
                $realm_count = $this->TemplateRealm->find('count',array('conditions' => array('TemplateRealm.template_id' => $template_id)));

                if($realm_count == 0){
                    $auth_data = $this->Session->read('AuthInfo');
                    Configure::load('yfi');
                    if($auth_data['Group']['name'] == Configure::read('group.ap')){
                       //AP's are NOT deleting something available to '(all)'!!!
                        continue;
                    }
                }
                //-------------------------------------------

                $this->Template->del($template_id,true);
                $this->TemplateRealm->deleteAll(array('TemplateRealm.template_id' => $template_id));
                $this->TemplateAttribute->deleteAll(array('TemplateAttribute.template_id' => $template_id));
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_vendors(){

        //----------------------------------------------------------------------------
        //--Description: List Vendors from FreeRADIUS Dictionary Files ---------------
        //--AP Sepcific: NA ----------------------------------------------------------
        //--Rights Completed:    NA --------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';

        $vendor_list    = $this->Freeradius->getVendors();

        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        $count = 0;
        foreach($vendor_list as $vendor){

            array_push($json_return['items'],array('id' => $count,'name' => $vendor));
            $count ++;
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_attributes_for_vendor($vendor){

        //----------------------------------------------------------------------------
        //--Description: Specify a Vendor and get attributes from FreeRADIUS----------
        //--AP Sepcific: NA ----------------------------------------------------------
        //--Rights Completed:    NA --------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';

        $attr_list    = $this->Freeradius->getAttributes($vendor);

        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        $count = 0;
        foreach($attr_list as $attr){

            array_push($json_return['items'],array('id' => $count,'name' => $attr));
            $count ++;
        }

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


     function json_edit($id){

        //----------------------------------------------------------------------------
        //--Description: Find TemplateAttributes for specified template (dojo grid)---
        //--AP Sepcific: TODO --------------------------------------------------------
        //--Rights Completed:   TODO -------------------------------------------------
        //----------------------------------------------------------------------------


        $this->layout = 'ajax';

        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        $q_r = $this->TemplateAttribute->find('all',array('conditions' => array('TemplateAttribute.template_id' => $id)));

        foreach($q_r as $item){

            $attr = $item['TemplateAttribute']['attribute'];
            $tt   = $item['TemplateAttribute']['tooltip']; 
            $type = $item['TemplateAttribute']['type']; 
            $unit = $item['TemplateAttribute']['unit']; 
            array_push($json_return['items'], array('id' => $attr, 'name' => $attr,'type' => $type ,'tooltip' => $tt,'unit' => $unit));
           // print_r($item);

        }


        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_attr_add($attribute_name,$template_id){

        //----------------------------------------------------------------------------
        //--Description: Add TemplateAttribute for specifies template ---------------- 
        //--AP Sepcific: TODO --------------------------------------------------------
        //--Rights Completed:   TODO -------------------------------------------------
        //----------------------------------------------------------------------------

         $this->layout = 'ajax';

        $d = array();
        $d['TemplateAttribute']['id']               = '';
        $d['TemplateAttribute']['template_id']      = $template_id;
        $d['TemplateAttribute']['attribute']        = $attribute_name;
        $d['TemplateAttribute']['type']             = 'Check';
        $d['TemplateAttribute']['tooltip']          = '--Tooltip Goes Here--';
        $d['TemplateAttribute']['unit']             = 'Text String';

        $this->TemplateAttribute->save($d);

        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


    function json_attr_edit(){

        //----------------------------------------------------------------------------
        //--Description: Edit TemplateAttribute for specifies template --------------- 
        //--AP Sepcific: TODO --------------------------------------------------------
        //--Rights Completed:   TODO -------------------------------------------------
        //----------------------------------------------------------------------------

		$template_id 	=  $this->params['url']['id'];
		$attribute_name	=  $this->params['url']['attr_name'];
		$field 			=  $this->params['url']['field'];
		$new_value 		= $this->params['url']['new_value'];


         $this->layout = 'ajax';

        //Firest find the entry
        $q_r = $this->TemplateAttribute->find('first',array('conditions' => array('TemplateAttribute.attribute' => $attribute_name,'TemplateAttribute.template_id' => $template_id)));

        $d = array();
        $d['TemplateAttribute']['id']               = $q_r['TemplateAttribute']['id'];
        $d['TemplateAttribute']["$field"]           = $new_value;
        $this->TemplateAttribute->save($d);

        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


     function json_attr_delete($id){

        //----------------------------------------------------------------------------
        //--Description: Delete TemplateAttributes for specifies template ------------ 
        //--AP Sepcific: TODO --------------------------------------------------------
        //--Rights Completed:   TODO -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';

        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $attribute = $this->params['url'][$key];
                $this->TemplateAttribute->deleteAll(array('TemplateAttribute.template_id' =>$id,'TemplateAttribute.attribute' => $attribute),true);
                //TODO Clean the template_attributes + template_realms
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
        $json_return['items']             = $this->Dojolayout->actions_for_templates();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_actions_view(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a template view tab------------ 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                       = 'ajax';
        $json_return['items']               = $this->Dojolayout->actions_for_template_view();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    //========================================================================================================================
    //===================== Private Functions ================================================================================
    //========================================================================================================================

    function _add_template_realm($template_id,$realm_id){

        $d = array();
        $d['TemplateRealm']['id']               = '';
        $d['TemplateRealm']['template_id']      = $template_id;
        $d['TemplateRealm']['realm_id']         = $realm_id;

        $this->TemplateRealm->save($d);
        //Get the Realm Name of the RealmID
        $qr = $this->Realm->findById($realm_id);
        $realm_name = $qr['Realm']['name'];
        return $realm_name;
    }


    function _getAttributeCount($tempate_attributes){

        $reply_count = 0;
        $check_count = 0;

        foreach($tempate_attributes as $entry){
            if($entry['type'] == 'Reply'){
                $reply_count ++;
            }
             if($entry['type'] == 'Check'){
                $check_count ++;
            }
        }
        return array('check_count' => $check_count, 'reply_count' => $reply_count);
    }

    function _getRealmsForTemplate($template_id){
    //Determine the realms for a template and if the current user (AccessProvider) has rights to view it

        $qr =$this->TemplateRealm->find('all',array('conditions' => array('TemplateRealm.template_id' => $template_id)));

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

}
?>