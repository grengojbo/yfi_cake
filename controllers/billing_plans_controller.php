<?
class BillingPlansController extends AppController {
    var $name       = 'BillingPlans';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json',);    //Add the locker component
    var $uses       = array('BillingPlan','BillingPlanRealm','Realm');

    var $scaffold;

    //------------------------------------------------------------------
    //--- List of rights that can be tweaked ---------------------------
    //------------------------------------------------------------------
    /*
        1.) json_add()
        2.) json_edit()
        3.) json_edit_promo()
        4.) json_edit_extra()
        5.) json_del()
    */
    //-----------------------------------------------------------------



    function json_index(){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------


        //-- Query the DB ---------------------------------------------
        $r = $this->BillingPlan->find(
                            'all',
                            array()
            );
        foreach($r as $entry){

           //// $attribute_count    = $this->_getAttributeCount($entry['Profile']['name']);

            //---------------------------------------------------------------------------------------------------------
            //this will return an array containing a 'show' and an 'available_to' key - if show == false will not show
            $return_array       = $this->_getRealmsForBillingPlan($entry['BillingPlan']['id']);
            //---------------------------------------------------------------------------------------------------------

            if($return_array['show']== true){
                array_push( $json_return['items'],
                            array(  'id'            => $entry['BillingPlan']['id'],
                                    'name'          => $entry['BillingPlan']['name'],
                                    'currency'      => $entry['BillingPlan']['currency'],
                                    'subscription'  => $entry['BillingPlan']['subscription'], 
                                    'time_unit'     => $entry['BillingPlan']['time_unit'],
                                    'data_unit'     => $entry['BillingPlan']['data_unit'],
                                    'tax'           => $entry['BillingPlan']['tax'],
                                    'realms'        => $return_array['available_to'],
                            )
                );

            } 
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }

    function json_add(){

        $this->layout = 'ajax';

        $d = array();

        $d['BillingPlan']['id']            = '';
        $d['BillingPlan']['name']          = $this->params['form']['name'];
        $d['BillingPlan']['currency']      = $this->params['form']['currency'];
        $d['BillingPlan']['subscription']  = $this->params['form']['subscription'];
        $d['BillingPlan']['time_unit']     = $this->params['form']['time_unit'];
        $d['BillingPlan']['data_unit']     = $this->params['form']['data_unit'];
        $d['BillingPlan']['tax']           = $this->params['form']['tax'];

        $this->BillingPlan->save($d);

        $json_return= array();

        if(array_key_exists('available_all',$this->params['form'])){

            //Available to all does not add any na_realm entries

        }else{

            foreach(array_keys($this->params['url']) as $key){
                if(preg_match('/^\d+/',$key)){
                    //----------------
                    $realm_id = $this->params['url'][$key];
                    $this->_add_billing_plan_realm($this->BillingPlan->id,$realm_id);
                    //-------------
                }
            }
        }
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }


     function json_view($id){

        $this->layout = 'ajax';

        $qr = $this->BillingPlan->findById($id);
        $json_return= array();
        $json_return['plan'] = $qr['BillingPlan'];

        //Check if it is asigned only to certain realms
        $json_return['plan']['available_to_all']   = false;
        if(count($qr['BillingPlanRealm'])< 1){
            $json_return['plan']['available_to_all']   = true;
        }

        //Manipulate the free data's units
        $json_return['plan']['data_units'] = 'kb';       //Start off with a Kb
        if($json_return['plan']['free_data'] >= (1024*1024*1024)){
            $json_return['plan']['data_units'] = 'gb';
            $json_return['plan']['free_data']  = $json_return['plan']['free_data'] / 1024 / 1024 /1024;
        }

        if($json_return['plan']['free_data'] >= (1024*1024)){
            $json_return['plan']['data_units'] = 'mb';
            $json_return['plan']['free_data']  = $json_return['plan']['free_data'] / 1024 / 1024;
        }
       

        //Manipulate the free time's units
        $json_return['plan']['time_units'] = 'm';       //Start off with a minutes
        if($json_return['plan']['free_time'] >= (60*60*24)){
            $json_return['plan']['time_units'] = 'd';
            $json_return['plan']['free_time']  = $json_return['plan']['free_time'] / 60 / 60 / 24;
        }

        if($json_return['plan']['free_time'] >= (60*60)){
            $json_return['plan']['time_units'] = 'h';
            $json_return['plan']['free_time']  = $json_return['plan']['free_time'] / 60 / 60;
        }

        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_edit(){

        $d = array();
        $this->layout                       = 'ajax';
        $billing_plan_id                    = $this->params['form']['id'];
        $d['BillingPlan']['id']             = $billing_plan_id;
        $d['BillingPlan']['name']           = $this->params['form']['name'];
        $d['BillingPlan']['currency']       = $this->params['form']['currency'];
        $d['BillingPlan']['subscription']   = $this->params['form']['subscription'];
        $d['BillingPlan']['time_unit']      = $this->params['form']['time_unit'];
        $d['BillingPlan']['data_unit']      = $this->params['form']['data_unit'];
        $d['BillingPlan']['tax']            = $this->params['form']['tax'];
        $this->BillingPlan->save($d);

        //Check if we need to remove any realms
        if(array_key_exists('available_all',$this->params['form'])){
            //Remove any existing NaRealm bindings
            $this->BillingPlanRealm->deleteAll(array('BillingPlanRealm.billing_plan_id'=> $billing_plan_id));

        }else{

            //Remove any existing NaRealm bindings
            $this->BillingPlanRealm->deleteAll(array('BillingPlanRealm.billing_plan_id'=> $billing_plan_id));
            //Get the list of realms passed to us
            foreach(array_keys($this->params['url']) as $key){
                if(preg_match('/^\d+/',$key)){
                    //----------------
                    $realm_id = $this->params['url'][$key];
                    $this->_add_billing_plan_realm($billing_plan_id,$realm_id);
                    //-------------
                }
            }
        }

        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_edit_promo(){

        $d = array();
        $this->layout                       = 'ajax';
        $billing_plan_id                    = $this->params['form']['id'];
        $d['BillingPlan']['id']             = $billing_plan_id;

        $data_multiplier                    = 1024;
        if($this->params['form']['data_units'] == 'mb'){
            $data_multiplier = $data_multiplier * 1024;
        }
        if($this->params['form']['data_units'] == 'gb'){
            $data_multiplier = $data_multiplier * 1024 * 1024;
        }
        $d['BillingPlan']['free_data']      = ($this->params['form']['free_data']* $data_multiplier);

        $time_multiplier                = 60;
        if($this->params['form']['time_units'] == 'h'){
            $time_multiplier = $time_multiplier * 60;
        }
        if($this->params['form']['time_units'] == 'd'){
            $time_multiplier = $time_multiplier * 60 * 24;
        }
        $d['BillingPlan']['free_time']      = ($this->params['form']['free_time']* $time_multiplier);
        $d['BillingPlan']['discount']       = $this->params['form']['discount'];
       
        $this->BillingPlan->save($d);
        $json_return['json']['status']      = "ok";
        $this->set('json_return',$json_return);
    }

    function json_edit_extra(){

        $d = array();
        $this->layout                       = 'ajax';
        $billing_plan_id                    = $this->params['form']['id'];
        $d['BillingPlan']['id']             = $billing_plan_id;
        $d['BillingPlan']['extra_time']     = $this->params['form']['extra_time'];
        $d['BillingPlan']['extra_data']     = $this->params['form']['extra_data'];
        $this->BillingPlan->save($d);
        $json_return['json']['status']      = "ok";
        $this->set('json_return',$json_return);
    }



     function json_del(){

        $this->layout = 'ajax';

        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $billing_plan_id = $this->params['url'][$key];

                //--------------------------------------------
                //See if this is available to all - then 'ap' can not delete it
                $realm_count = $this->BillingPlanRealm->find('count',array('conditions' => array('BillingPlanRealm.billing_plan_id' => $billing_plan_id)));

                if($realm_count == 0){
                    $auth_data = $this->Session->read('AuthInfo');
                    Configure::load('yfi');
                    if($auth_data['Group']['name'] == Configure::read('group.ap')){
                       //AP's are NOT deleting something available to '(all)'!!!
                        continue;
                    }
                }
                //-------------------------------------------
                $this->BillingPlan->del($billing_plan_id,true);
                $this->BillingPlanRealm->deleteAll(array('BillingPlanRealm.billing_plan_id' => $billing_plan_id));

            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

     function json_realms_for_plan($planId){

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
            $count = $this->BillingPlanRealm->find('count', array('conditions' => array('BillingPlanRealm.billing_plan_id' => $planId,'BillingPlanRealm.realm_id' => $entry['Realm']['id'])));
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
        $json_return['items']             = $this->Dojolayout->actions_for_billing_plans();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }

    //========================================================================================================================
    //===================== Private Functions ================================================================================
    //========================================================================================================================
    function _add_billing_plan_realm($billing_plan_id,$realm_id){

        $d                                          = array();
        $d['BillingPlanRealm']['id']                = '';
        $d['BillingPlanRealm']['billing_plan_id']   = $billing_plan_id;
        $d['BillingPlanRealm']['realm_id']          = $realm_id;
        $this->BillingPlanRealm->save($d);
        $this->BillingPlanRealm->id                 = false;
    }


    function _getRealmsForBillingPlan($billing_plan_id){
    //Determine the realms for a billing plan and if the current user (AccessProvider) has rights to view it

        $qr =$this->BillingPlanRealm->find('all',array('conditions' => array('BillingPlanRealm.billing_plan_id' => $billing_plan_id)));

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