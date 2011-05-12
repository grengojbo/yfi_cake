<?
class NotesController extends AppController {
    var $name       = 'Notes';
    var $helpers    = array('Javascript');

    var $components = array('Session');    //Add the locker component
    var $uses       = array('Note','Section');

    var $scaffold;

    function json_index($user_id = null){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'mac';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        $q_r = $this->Note->find('all',array('conditions' => array('User.id' => $user_id)));

        foreach($q_r as $note){

            //print_r($note);
            array_push($json_return['items'],array('id'=>$note['Note']['id'],'section' => $note['Section']['name'],'value' => $note['Note']['value'], 'date' => $note['Note']['created']));
        }

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }

    function json_section_list(){

        //Returns a lsit of prepaid users for specified realm
         $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok';
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //-- Query the DB ---------------------------------------------
        $r = $this->Section->find('all',array());

        $counter = 0;
        foreach($r as $item){
            //Add the abiltiy to 'unassign' a credit
            if($counter == 0){
                array_push($json_return['items'],array('name' => $item['Section']['name'], 'id' => $item['Section']['id'],'selected' => 'selected')); //Select the first one
            }else{
                array_push($json_return['items'],array('name' => $item['Section']['name'], 'id' => $item['Section']['id'])); //Select the first one
            }
        }

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }

    function json_add(){

        $this->layout = 'ajax';


        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $user_id = $this->params['url'][$key];
                $d = array();
                $d['Note']['user_id']       = $user_id;
                $d['Note']['section_id']    = $this->params['form']['section'];
                $d['Note']['value']         = $this->params['form']['note'];
                //-------------
                $this->Note->save($d);
                $this->Note->id  =false;    //Clear the ID
            }
        }
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

     function json_del(){

        $this->layout = 'ajax';
        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $note_id = $this->params['url'][$key];
                $this->Note->del($note_id,false);
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

}
?>