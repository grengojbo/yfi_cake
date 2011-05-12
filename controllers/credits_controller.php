<?
class CreditsController extends AppController {
    var $name       = 'Credits';

    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json','CmpPermanent');    //Add the locker component
    var $uses       = array('Credit','User','Radacct','Radcheck','Radgroupcheck','Radusergroup');

    var $scaffold;

    function json_index(){

        $this->layout = 'ajax';
        $json_return    = array();
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
        //--http://127.0.0.1/c2/yfi_cake/credits/json_index/1261036591452?id=4b*&start=0&count=40----
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

        $list   = $this->Credit->find('all',array(
                                                    'conditions' => $conditions
                                                    )
                                                );

        //Required for the QueryDataStore (Dojo)
        $json_return['numRows']    = count($list);

        //Now we filted only the required page
        if(($start != '')&($count != '')){

             $list   = $this->Credit->find('all',array(
                                                    'conditions'    => $conditions,
                                                    'limit'         => $count,
                                                    'page'          => $page,
                                                    'order'         => $sort 
                                                    )
                                                );

        }

        foreach($list as $item){

            $id     = $item['Credit']['id'];
            $realm  = $item['Realm']['name'];
            $r_id   = $item['Realm']['id'];
            $expire = $item['Credit']['expires'];
            $creator= $item['User']['username'];
            $data   = $item['Credit']['data'];
            $time   = $item['Credit']['time'];
            $att    = $item['UsedBy']['username'];  
            array_push($items,array('id'=> $id,'realm'=> $realm, 'expires'=>$expire,'creator' => $creator,'data'=> $data,'time' => $time,'realm_id' => $r_id,'attached' => $att));
        }

        //---Prepare the JSON--------------------
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = $items;
        //-----------------------------------------

        $this->set('json_return',$json_return);

    }


    function json_user_index($user_id){

        $this->layout = 'ajax';
        $json_return    = array();
        #---------------------------------------------------------------------------------------------------------
        #--------This is one of the most complex methods of the controller. It has to take quite a lot in account-
        #---------------------------------------------------------------------------------------------------------

        //TODO: FIX THE RIGHTS
        //--------Check the rights------
       // if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
       //     $this->set('json_return',$this->Json->permFail());
       //     return;
       // }
        //-------------------------------------
        $items  = array();
        $list   =   $this->Credit->find('all',array('conditions'=> array('Credit.used_by_id' => $user_id),'order'         => array('Credit.modified DESC')));

        foreach($list as $item){

            $id     = $item['Credit']['id'];
            $expire = $item['Credit']['expires'];
            $data   = $item['Credit']['data'];
            $time   = $item['Credit']['time'];
            $r_id   = $item['Realm']['id'];
            array_push($items,array('id'=> $id, 'expires'=>$expire,'data'=> $data,'time' => $time,'realm_id' => $r_id,));
        }

        //---Prepare the JSON--------------------
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = $items;
        //-----------------------------------------

        $this->set('json_return',$json_return);

    }


	function json_add(){

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

		//Get the user_id of the user who's logged in right now
		$user_id = $this->Auth->user('id');

		//Get the units
		$du 	= $this->params['form']['data_units'];
		$tu 	= $this->params['form']['time_units'];

		//Data multiply
		$data	= $this->params['form']['dat'];
		if($du == 'kb'){
			$data = ($data * 1024);			
		}
		if($du == 'mb'){
			$data = ($data * 1024 * 1024);
		}
		if($du == 'gb'){
			$data = ($data * 1024 * 1024 * 1024);
		}

		//Time multiply
		$time	= $this->params['form']['time'];
		if($tu == 'm'){
			$time = ($time * 60);			
		}
		if($tu == 'h'){
			$time = ($time * 60 * 60);
		}
		if($tu == 'd'){
			$time = ($time * 60 * 60 * 24);
		}

        $d                                  = array();
        $d['Credit']['id']            		= '';
        $d['Credit']['user_id']       		= $user_id;
        $d['Credit']['realm_id']          	= $this->params['form']['realm'];
		$d['Credit']['expires']          	= $this->params['form']['expires'];
        $d['Credit']['data']   				= $data;
		$d['Credit']['time']   				= $time;

        $this->Credit->save($d);

        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);

    }


	function json_attach($ic_id){

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }

        //It may be that this credit was assigned to someone else - they also need to be updated - check first
        $qr     = $this->Credit->find('first',array('conditions' => array('Credit.id' => $ic_id)));
		$d							= array();
		$d['Credit']['id']			= $ic_id;

        if($this->params['form']['user'] == 0){
            $d['Credit']['used_by_id'] = null;  //To un-asighn a credit
        }else{
            $d['Credit']['used_by_id']	= $this->params['form']['user'];
        }
		$this->Credit->save($d);

        //Update if it was attached to someone before (Updtate that user's usage)
        if($qr['Credit']['used_by_id'] != ''){
           $this->CmpPermanent->update_user_usage($qr['Credit']['used_by_id']); 
        }
         
        //Update the usage
        if($this->params['form']['user'] != 0){ //Do not update anyone if it is un-assgned
            $this->CmpPermanent->update_user_usage($this->params['form']['user']);
        }

        //-------------------------------------
	  	$json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);

    }

    function json_view($ic_id){

        $this->layout = 'ajax';
        $json_return= array();

        $qr = $this->Credit->findById($ic_id);
        
        $json_return['internet_credit']['id']       = $qr['Credit']['id'];

        //Get the expiry date:
        $exp    = $qr['Credit']['expires'];
        $e      = explode(" ",$exp);

        //Get the data unit Start with the smalest
        $data       = $qr['Credit']['data'];
        $ret_data   = $data / 1024;
        $d_unit     = 'kb';

        if(($data / 1024 /1024 )>= 1 ){
            $ret_data   = $data / 1024 / 1024;
            $d_unit     = 'mb';
        }

        if(($data /1024 /1024/1024) >= 1){
            $ret_data   = $data / 1024 / 1024 /1024;
            $d_unit     = 'gb';
        }

        $time       = $qr['Credit']['time'];
        $ret_time   = $time / 60;
        $time_unit  = 'm';

        if(($time /60 /60)> 1){
            $ret_time   = $time / 60 /60;
            $time_unit  = 'h';
        }

        if(($time /60 /60 /24)>1){
            $ret_time   = $time /60 /60 /4;
            $time_unit  = 'd';
        }

        $json_return['internet_credit']['expires']      = $e[0];
        $json_return['internet_credit']['dat']          = $ret_data;
        $json_return['internet_credit']['data_unit']    = $d_unit;
        $json_return['internet_credit']['time']         = $ret_time;
        $json_return['internet_credit']['time_unit']    = $time_unit;
        $json_return['json']['status'] = "ok";

        $this->set('json_return',$json_return);

    }

    function json_edit($id){

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        //Get the user_id of the user who's logged in right now
        $user_id = $this->Auth->user('id');

        //Get the units
        $du     = $this->params['form']['data_units'];
        $tu     = $this->params['form']['time_units'];

        //Data multiply
        $data   = $this->params['form']['dat'];
        if($du == 'kb'){
            $data = ($data * 1024);         
        }
        if($du == 'mb'){
            $data = ($data * 1024 * 1024);
        }
        if($du == 'gb'){
            $data = ($data * 1024 * 1024 * 1024);
        }

        //Time multiply
        $time   = $this->params['form']['time'];
        if($tu == 'm'){
            $time = ($time * 60);           
        }
        if($tu == 'h'){
            $time = ($time * 60 * 60);
        }
        if($tu == 'd'){
            $time = ($time * 60 * 60 * 24);
        }

        $d                                  = array();
        $d['Credit']['id']                  = $id;
        $d['Credit']['user_id']             = $user_id;
        $d['Credit']['expires']             = $this->params['form']['expires'];
        $d['Credit']['data']                = $data;
        $d['Credit']['time']                = $time;

        $this->Credit->save($d);

        //Get the user id to which this credit is assigned
        $qr = $this->Credit->find('first',array('conditions' =>array('Credit.id' => $id)));
        $used_by_id = $qr['Credit']['used_by_id'];

        if($used_by_id != ''){
            //Update the usage
            $this->CmpPermanent->update_user_usage($used_by_id);
        }


        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);

    }

    function json_del(){
        $this->layout = 'ajax';

        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $id = $this->params['url'][$key];
                $qr = $this->Credit->find('first', array('conditions' => array('Credit.id' => $id )));
                $used_by_id = $qr['Credit']['used_by_id'];

                $this->Credit->delete($id);

                //We need to update the user's usage AFTER we delete
                if($used_by_id != ''){
                    //Update the usage
                    $this->CmpPermanent->update_user_usage($used_by_id);
                }
                //-------------
            }
        }

        
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }


     function _returnSearchFilterConditions(){

        //----------------Search Filter ----------------------
        $column;
        $condition;

        if(array_key_exists('id',$this->params['url'])){
            $column    = 'Credit.id';
            $condition  = $this->params['url']['id'];
        }

        if(array_key_exists('realm',$this->params['url'])){
            $column    = 'Realm.name';
            $condition  = $this->params['url']['realm'];
        }

        if(array_key_exists('expires',$this->params['url'])){
            $column    = 'Credit.expires';
            $condition  = $this->params['url']['expires'];
        }

        if(array_key_exists('creator',$this->params['url'])){
            $column    = 'User.username';
            $condition  = $this->params['url']['creator'];
        }

        if(array_key_exists('attached',$this->params['url'])){

            $column     = 'UsedBy.username';
            $condition  = $this->params['url']['attached'];
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
                    $realm_name = $realm_line['name'];
                    array_push($realm_filter,array("Realm.name " => $realm_name));
                }
            }

            array_push($conditions,array('or' => $realm_filter));

            //--------------------------
            //Access Providers will by default only view credits created by them
            //This makes it nice for branches eg an AP is assigned to a branch and only manages their credits
            //A Manager then can view all users inside a realm
            //**PERMISSION 'credits/only_view_own'
            //**FUNCTION Only list the credits an Access Provider created them self
            if($this->_look_for_right('credits/only_view_own')){       #FIXME Change to users....
                    $user_id = $auth_info['User']['id'];
                    array_push($conditions,array("Credit.user_id" => $user_id)); //Add This AND filtertjie
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

            if(preg_match('/id/',$sort)){

                $s = "Credit.id $sort_order";
            }

            if(preg_match('/realm/',$sort)){

                $s = "Realm.name $sort_order";
            }

             if(preg_match('/expires/',$sort)){

                $s = "Credit.expires $sort_order";
            }

            if(preg_match('/data/',$sort)){

                $s = "Credit.data $sort_order";
            }

            if(preg_match('/time/',$sort)){

                $s = "Credit.time $sort_order";
            }

            if(preg_match('/creator/',$sort)){

                $s = "User.username $sort_order";
            }

            if(preg_match('/attached/',$sort)){

                $s = "UsedBy.username $sort_order";
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

        $json_return['items']             = $this->Dojolayout->actions_for_credits();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

     function json_user_actions(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on profiles tab------------------ 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';

        $json_return['items']             = $this->Dojolayout->actions_for_user_credits();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    //========================================================================================================================
    //===================== Private Functions ================================================================================
    //========================================================================================================================

}
?>