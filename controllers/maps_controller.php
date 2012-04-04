<?
class MapsController extends AppController {
    var $name       = 'Maps';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights');    //Add the locker component
    var $uses       = array('Map');

   // var $scaffold;

	 function json_index($user_id){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']      = 'ok';

        $json_return['maps']                = array();

        //Get Sane defaults
        $json_return['maps']['lat']     = -25.74470879185722;
        $json_return['maps']['lon']     =  28.27759087085724;
        $json_return['maps']['type']    = 'G_SATELLITE_MAP';
        $json_return['maps']['zoom']    = 13;

        $qr = $this->Map->findAllByUserId($user_id);

        foreach($qr as $setting){
            ($setting['Map']['name'] == 'lat'          )&&($json_return['maps']['lat']=$setting['Map']['value']);
            ($setting['Map']['name'] == 'lon'          )&&($json_return['maps']['lon']=$setting['Map']['value']);
            ($setting['Map']['name'] == 'type'         )&&($json_return['maps']['type']=$setting['Map']['value']);
            ($setting['Map']['name'] == 'zoom'         )&&($json_return['maps']['zoom']=$setting['Map']['value']);
        }

        $json_return['maps']['items']   = array();
        array_push($json_return['maps']['items'], array('id' => 10, 'available' => true,    'lon' => 28.27796515789032, 'lat' => -25.74490879185922));
        array_push($json_return['maps']['items'], array('id' => 20, 'available' => false,   'lon' => 28.27706515789032, 'lat' => -25.74400879185700));

        $this->set('json_return',$json_return);

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


    function json_edit(){
        $this->layout = 'ajax';

        $auth_data  = $this->Session->read('AuthInfo');
        $id         = $auth_data['User']['id'];

        $lon      = $this->params['form']['lon'];
        $lat      = $this->params['form']['lat'];
        $zoom     = $this->params['form']['zoom'];
        $type     = $this->params['form']['type'];

        $this->_set_map_value($id,'lon', $lon);
        $this->_set_map_value($id,'lat', $lat);
        $this->_set_map_value($id,'zoom', $zoom);
        $this->_set_map_value($id,'type', $type);

        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    
    function _set_map_value($user_id,$name,$value){

        $qr = $this->Map->find('first',array('conditions'=> array('Map.user_id' => $user_id,'Map.name' => $name)));
        //print_r($qr);
        $id = '';
        if($qr != ''){
            $id = $qr['Map']['id'];
        }

        $d['Map']['id']                 = $id;
        $d['Map']['user_id']            = $user_id;
        $d['Map']['name']               = $name;
        $d['Map']['value']              = $value;
        $this->Map->save($d);
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

        $json_return['items']             = $this->Dojolayout->actions_for_maps();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

}
?>