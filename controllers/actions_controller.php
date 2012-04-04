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

    function json_actions(){

        $this->layout                     = 'ajax';

        $json_return['items'] = array(
                                        array('name'=>"Reload List","type"=>"reload","action"=>"reload"),
                                       // array("name"=>"Edit Selected","type"=>"edit","action"=>"edit"),
                                        array("name"=>"Add","type"=>"add","action"=>"add"),
                                        array("name"=>"Delete Selected","type"=>"delete","action"=>"del")
                                );

        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_index($nas_id=null){  //For the dojo Grid
       
        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'action';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        $qr = $this->Action->find('all',array('conditions' => array('Action.na_id' => $nas_id),'order' => array('Action.modified DESC')));
        foreach($qr as $item){
           // print_r($item);
            array_push($json_return['items'],$item['Action']);
        }

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

     function json_add($id){

        $this->layout           = 'ajax';
        $command                = $this->params['form']['command']; 
        $d                      = array();
        $d['Action']['na_id']   = $id;
        $d['Action']['command'] = $command;
        $this->Action->save($d);
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_del(){
        $this->layout = 'ajax';

        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $id = $this->params['url'][$key];
                $this->Action->delete($id);
            }
        }
    
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

}
?>
