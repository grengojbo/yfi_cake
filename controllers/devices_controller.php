<?
class DevicesController extends AppController {
    var $name       = 'Devices';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights');    //Add the locker component
    var $uses       = array('Device');

   // var $scaffold;

	 function json_index($user_id){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'username';
        $json_return['identifier']        = 'id';
        //print_r($list);
        $items = array();

        $qr = $this->Device->findAllByUserId($user_id);
        foreach($qr as $item){

			if($item['Device']['created'] == $item['Device']['modified']){
				$last_contact = 'never';
			}else{
				$last_contact = $item['Device']['modified'];

			}

            array_push($items, array(
                                        'id'        => $item['Device']['id'],
                                        'created'   => $item['Device']['created'],
                                        'name'     => $item['Device']['name'],
                                        'description'=> $item['Device']['description'],
										'modified'   => $item['Device']['modified'],
										'last_contact'=> $last_contact,
                                )
            );
        }

        $json_return['items']             = $items;

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

	function json_add($user_id){
        $this->layout = 'ajax';
        $d                                  = array();
        $d['Device']['id']            		= '';
        $d['Device']['user_id']       		= $user_id;
        $device_name                        = strtoupper($this->params['form']['mac']);
        $d['Device']['name']          		= $device_name;
        $d['Device']['description']   		= $this->params['form']['description'];

        $json_return= array();
        //First check if there is not already such a device
        $count = $this->Device->find('count',array('conditions' => array('Device.name' => $device_name )));
        if($count == 0){
            $json_return['json']['status'] = "ok";
            $this->Device->save($d);
        }else{
            $json_return['json']['status'] = "error";
            $json_return['json']['detail'] = gettext("Device already exists");
        }
        $this->set('json_return',$json_return);
    }

	 function json_del(){
        $this->layout = 'ajax';

        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $this->Device->delete($this->params['url'][$key]);
                //-------------
            }
        }
        $json_return= array();
        $json_return['json']['status'] = "ok";
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

        $json_return['items']             = $this->Dojolayout->actions_for_devices();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }


    function json_user_portal_actions(){

        $this->layout = 'ajax';

        $json_return['items']             = $this->Dojolayout->actions_for_devices();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }



}
?>
