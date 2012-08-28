<?
class CcTransactionsController extends AppController {
    var $name       = 'CcTransactions';

    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json','CmpPermanent');    //Add the locker component
    var $uses       = array('CcTransaction','TransactionDetail');

    var $scaffold;

    function beforeFilter() {
       $this->Auth->allow(
		'submit_transaction',
        'info_for'
        );
    }

    function submit_transaction(){

        $this->layout   = 'ajax'; //To send your own content
        //We start by finding out who try to post, we will only allow form known defined IP addresses.
        $allow_from = array('127.0.0.1','66.185.185.5');

        $request_from   = $_SERVER["REMOTE_ADDR"];

        //Set it by default to false and error
        $this->set('error',false);  
        $this->set('trans_id',"error");

        if (!in_array("$request_from", $allow_from)) {
            $this->set('error',"Not processing any requests from: $request_from");
            return;
        }
        
        //If we are allowed; we have to loop through all the post elements and record those with a value 
        //(once we have added a new transaction)

        $d                                  = array();
        $d['CcTransaction']['ip_address']   = $request_from;

        $this->CcTransaction->create();
        $this->CcTransaction->save($d);
        
        $transaction_id = $this->CcTransaction->id;
        $this->set('trans_id',$transaction_id);

        //Record the posted pairs for this transaction
        $array_keys = array_keys($_POST);
        foreach($array_keys as $key){
            if($_POST[$key] != ''){ //Only the ones with values
                $td = array();
                $td['TransactionDetail']['name']                = $key;
                $td['TransactionDetail']['value']               = $_POST[$key];
                $td['TransactionDetail']['cc_transaction_id']   = $transaction_id ;
                $this->TransactionDetail->create();
                $this->TransactionDetail->save($td);
                $this->TransactionDetail->id = false;
            }  
        }
    }

    function info_for(){
        //This should be called in a JSONP manner supplying a callback function to pad with
        
         $this->layout = 'ajax';     
        //http://127.0.0.1/c2/yfi_cake/cc_transactions/info_for?callback=callback&id=503d0df1-2c20-4633-95f3-03dea509ff00

        $json_return                = array();   //Fail it by default
        $json_return['success']     = false;
        $json_return['transaction'] = array();

        if(array_key_exists('id',$this->params['url'])){
            $id      = $this->params['url']['id'];
            $q_r     = $this->CcTransaction->findById($id);

            //Required fields to feedback
            $rf = array('x_auth_code',
                        'x_trans_id',
                        'x_type',
                        'x_amount',
                        'x_response_code',
                        'x_response_reason_code',
                        'x_response_reason_text',
            );
            if($q_r ==''){
                $this->set('json_return',$json_return);
                return;
            }
            foreach($q_r['TransactionDetail'] as $item){
                $name = $item['name'];
                if(in_array($name,$rf)){
                    $json_return['transaction'][$name] = $item['value'];
                }
            }
            $json_return['success']     = true;
        }

        $this->set('json_return',$json_return);
        $callback   = $this->params['url']['callback'];
        $this->set('json_pad_with',$callback);
    }
}
?>
