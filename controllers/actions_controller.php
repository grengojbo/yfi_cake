<?
class ActionsController extends AppController {
    var $name       = 'Actions';
    var $helpers    = array('Javascript');

    var $components = array('Session','Rights','Json','Dojolayout');    //Add the locker component
    var $uses       = array('Action','Na');

    function beforeFilter() {

       $this->Auth->allow('json_actions_for');       //Comment out to remove public display of Google Map overlay
     
    }

    var $scaffold;

    function json_actions_for($nas_id=null){  //For the dojo Grid
       
        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'action';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        $qr = $this->Action->find('all',array('conditions' => array('Action.na_id' => $nas_id)));
        foreach($qr as $item){
           // print_r($item);
            array_push($json_return['items'],$item['Action']);
        }



        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

}
?>
