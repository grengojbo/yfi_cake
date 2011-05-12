<?
class ExtraServicesController extends AppController {
    var $name       = 'ExtraServices';

    var $helpers    = array('Javascript');
    var $uses       = array('User','ExtraService');   //Tables to use
    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component


    //------------------------------------------------------------------
    //--- List of rights that can be tweaked ---------------------------
    //------------------------------------------------------------------
    /*
        1.) json_add()
        2.) json_del()
        3.) json_edit()
    */
    //-----------------------------------------------------------------


    function json_index($user_id){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'username';
        $json_return['identifier']        = 'id';
        //print_r($list);
        $items = array();

        $qr = $this->ExtraService->findAllByUserId($user_id);
        foreach($qr as $item){

            array_push($items, array(
                                        'id'        => $item['ExtraService']['id'],
                                        'created'   => $item['ExtraService']['created'],
                                        'title'     => $item['ExtraService']['title'],
                                        'description'=> $item['ExtraService']['description'],
                                        'amount'    => $item['ExtraService']['amount']
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
        $d['ExtraService']['id']            = '';
        $d['ExtraService']['user_id']       = $user_id;
        $d['ExtraService']['created']       = $this->params['form']['created'];
        $d['ExtraService']['title']         = $this->params['form']['title'];
        $d['ExtraService']['description']   = $this->params['form']['description'];
        $d['ExtraService']['amount']        = $this->params['form']['amount'];
        $this->ExtraService->save($d);
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_del(){
        $this->layout = 'ajax';

        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $this->ExtraService->delete($this->params['url'][$key]);
                //-------------
            }
        }
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }


     function json_view($extra_service_id){

        $this->layout = 'ajax';
        $json_return= array();

        $qr = $this->ExtraService->findById($extra_service_id);
        
        $json_return['extra_service']['id']       = $qr['ExtraService']['id'];
        $json_return['extra_service']['created']  = $qr['ExtraService']['created'];
        $json_return['extra_service']['title']    = $qr['ExtraService']['title'];
        $json_return['extra_service']['description'] = $qr['ExtraService']['description'];
        $json_return['extra_service']['amount']   = $qr['ExtraService']['amount'];
        $json_return['json']['status'] = "ok";

        $this->set('json_return',$json_return);
    }

     function json_edit($extra_service_id){
        $this->layout = 'ajax';
        $d                                  = array();
        $d['ExtraService']['id']            = $extra_service_id;
        $d['ExtraService']['created']       = $this->params['form']['created'];
        $d['ExtraService']['title']         = $this->params['form']['title'];
        $d['ExtraService']['description']   = $this->params['form']['description'];
        $d['ExtraService']['amount']        = $this->params['form']['amount'];
        $this->ExtraService->save($d);
        $json_return= array();
        $json_return['json']['status'] = "ok";
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
        $json_return['items']             = $this->Dojolayout->actions_for_extra_services();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }

}
?>