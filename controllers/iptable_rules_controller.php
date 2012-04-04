<?
class IptableRulesController extends AppController {
    var $name       = 'IptableRules';
    var $helpers   = array('Javascript');

    var $uses       = array('IptableRule');   //Tables to check for recent changes
    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component

    function json_index($profile_id){

        $this->layout = 'ajax';
        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['label']             = 'profile';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        $qr = $this->IptableRule->find('all',array('conditions' =>array('IptableRule.profile_id' => $profile_id), 'order' => 'IptableRule.priority DESC'));
        //print_r($qr);

        if($qr != ''){
            foreach($qr as $item){
                $id         = $item['IptableRule']['id'];
                $profile    = $item['Profile']['name'];
                $priority   = $item['IptableRule']['priority'];
                $action     = $item['IptableRule']['action'];
                $destination= $item['IptableRule']['destination'];
                $protocol   = $item['IptableRule']['protocol'];
                $port       = $item['IptableRule']['port'];

                array_push($json_return['items'],array('id' => $id,'profile' => $profile, 'priority' => $priority, 'action' => $action, 'destination' => $destination, 'protocol' => $protocol, 'port' => $port));
            }
        }
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_add(){

        $this->layout   = 'ajax';
        $d = array();
        $d['IptableRule']['id']             = '';
        $d['IptableRule']['profile_id']     = $this->params['form']['id'];
        $d['IptableRule']['priority']       = $this->params['form']['priority'];
        $d['IptableRule']['action']         = $this->params['form']['action'];
        $d['IptableRule']['destination']    = $this->params['form']['destination'];
        $d['IptableRule']['protocol']       = $this->params['form']['protocol'];
        $d['IptableRule']['port']           = $this->params['form']['port'];
        $this->IptableRule->save($d);

        $json_return    = array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

   function json_view($id){

        $this->layout = 'ajax';
        $json_return= array();
        $qr = $this->IptableRule->findById($id);
        $json_return['iptable_rule']['id']          = $id;
        $json_return['iptable_rule']['profile_id']  = $qr['IptableRule']['profile_id'];
        $json_return['iptable_rule']['priority']    = $qr['IptableRule']['priority'];
        $json_return['iptable_rule']['action']      = $qr['IptableRule']['action'];
        $json_return['iptable_rule']['destination'] = $qr['IptableRule']['destination'];
        $json_return['iptable_rule']['protocol']    = $qr['IptableRule']['protocol'];
        $json_return['iptable_rule']['port']        = $qr['IptableRule']['port'];
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

     function json_edit(){

        $this->layout   = 'ajax';
        $d = array();
        $d['IptableRule']['id']             = $this->params['form']['id'];
        $d['IptableRule']['priority']       = $this->params['form']['priority'];
        $d['IptableRule']['action']         = $this->params['form']['action'];
        $d['IptableRule']['destination']    = $this->params['form']['destination'];
        $d['IptableRule']['protocol']       = $this->params['form']['protocol'];
        $d['IptableRule']['port']           = $this->params['form']['port'];
        $this->IptableRule->save($d);

        $json_return    = array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }



     function json_del(){

        $this->layout = 'ajax';
         foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                 $rule_id = $this->params['url'][$key];
                 //----------------
                 $this->IptableRule->delete($rule_id);
                //-------------
            }
        }
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function txt_rules_for_user($user){

        $this->layout = 'ajax';

    }


}
?>