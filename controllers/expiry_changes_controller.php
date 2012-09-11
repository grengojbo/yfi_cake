<?
class ExpiryChangesController extends AppController {
    var $name       = 'ExpiryChanges';

    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component
    var $uses       = array(
                        'ExpiryChange'
                    );

    var $scaffold;

    var $required_fields = array('x_cust_id', 'x_amount', 'x_description', 'x_response_code');

    function beforeFilter() {
       $this->Auth->allow('*');
    }
    
    function json_index(){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'username';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        $q_r = $this->ExpiryChange->find('all');
       // print_r($q_r);
        foreach($q_r as $i){
            array_push($json_return['items'], array(
                'id'                    => $i['ExpiryChange']['id'],
                'cc_transaction_id'     => $i['CcTransaction']['id'],
                'initiator'             => $i['Initiator']['username'],
                'username'              => $i['User']['username'],
                'old_value'             => $i['ExpiryChange']['old_value'],
                'new_value'             => $i['ExpiryChange']['new_value'],
                'created'               => $i['ExpiryChange']['created'],
            ));

        }

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


}
?>
