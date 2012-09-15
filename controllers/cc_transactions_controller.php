<?
class CcTransactionsController extends AppController {
    var $name       = 'CcTransactions';

    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component
    var $uses       = array(
                        'CcTransaction',
                        'TransactionDetail',
                        'User',
                        'Radcheck',
                        'Realm',
                        'ExpiryChange'
                    );

    var $scaffold;

    var $required_fields = array(
            'x_cust_id',        //e.g dvdwalt@ri
            'x_amount',         //e.g 20.00
            'x_description',    //e.g. fast_20
            'x_response_code'   //Must be one
        );

    function beforeFilter() {
       $this->Auth->allow(
		'submit_transaction',
        'info_for'
        );
    }

   

    function submit_transaction(){
        //http://127.0.0.1/c2/yfi_cake/cc_transactions/submit_transaction

        //-------------------------------------------------------
        //----- READ HERE -> IMPORTANT --------------------------
        //---- In order to process this incomming transaction correct the following fields must be in the POST
        //-- 1.) x_cust_id -> This will be the username 
        //                  since Authorize.net only allow 20Characters for this field we cant use the user id
        //-- 2.) x_amount  -> This is the amount that was payed
        //-- 3.) x_description -> The description of what was purchased
        //-- 4.) x_response_code => This must be == 1 else it indicates a problem with the transaction
        //_______________________________________________________

        $this->layout   = 'ajax'; //To send your own content
        //We start by finding out who try to post, we will only allow form known defined IP addresses.
        $allow_from = array('127.0.0.1','66.185.185.5','192.168.1.112');

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

        //We also need to deterimine now what the person want to buy:
        //We will look at x_description and x_amount combination
        
        $required_present = false;
        foreach($this->required_fields as $field){
            if(!array_key_exists($field,$_POST)){
                $required_present = false;
                break;  //We don't need to continue even
            }else{
                $required_present = true;  
            }
        }
        if($required_present){
            $this->adjustExpiration($transaction_id);
        }
    }

    function info_for(){
        //This should be called in a JSONP manner supplying a callback function to pad with
        
         $this->layout = 'ajax';     
        //http://127.0.0.1/c2/yfi_cake/cc_transactions/info_for?callback=callback&id=503d0df1-2c20-4633-95f3-03dea509ff00

        $json_return                = array();   //Fail it by default
        $json_return['success']     = false;
        $json_return['transaction'] = array();

        //----Dummy Data---------
        $json_return['transaction'] = array(
            'x_auth_code'   => 'QCM5NU',
            'x_trans_id'    => '2175459845',
            'x_amount'      => '10.99',
            'x_card_type'   => 'Discover',
            'x_response_code' => 1,
            'x_response_reason_code' => 1,
            'x_response_reason_text' => 'This transaction has been approved.',
        );

        //--- End Dummy Data ----
/*
        if(array_key_exists('id',$this->params['url'])){
            $id      = $this->params['url']['id'];
            $q_r     = $this->CcTransaction->findById($id);

            //Required fields to feedback
            $rf = array('x_auth_code',
                        'x_trans_id',
                        'x_card_type',
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
*/
        $json_return['success']     = true;

        $this->set('json_return',$json_return);
        $callback   = $this->params['url']['callback'];
        $this->set('json_pad_with',$callback);
    }

    
    function json_index(){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'username';
        $json_return['identifier']        = 'id';
        


        //$conditions     = $this->_returnSearchFilterConditions();
       // $conditions     = array('TransactionDetail.name' => 'x_cust_id','TransactionDetail.value LIKE' => 'd%');
        $conditions     = array('TransactionDetail.name' => 'x_cust_id');
        $sort           = $this->_returnOrderClause();

        $realm_lookup   = array();

        //----Lets get to the correct Page------
        $start =0;
        $count ='';

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
        $list   = $this->TransactionDetail->find('all',array(
                                                    'conditions' => $conditions
                                                    )
                                                );

        //Required for the QueryDataStore (Dojo)
        $json_return['numRows']    = count($list);

        //Now we filted only the required page
        if(($start != '')&($count != '')){
             $list   = $this->TransactionDetail->find('all',array(
                                                    'conditions'    => $conditions,
                                                    'limit'         => $count,
                                                    'page'          => $page,
                                                    'order'         => $sort 
                                                    )
                                                );

            //We have to manipulate this list....
            foreach($list as $l){
                $username       = $l['TransactionDetail']['value'];
                $id             = $l['TransactionDetail']['cc_transaction_id'];
                $ip             = $l['CcTransaction']['ip_address'];
                $created        = $l['CcTransaction']['created'];

                //--We need to find the other detail of the transaction--
                $td_x_amount                = 0.00;
                $td_x_response_code         = '';
                $td_x_response_reason_code  = '';
                $td_x_response_reason_text  = '';

                $cc_t   = $this->CcTransaction->findById($id);

                foreach($cc_t['TransactionDetail'] as $td){
                    if($td['name'] === 'x_amount'){
                        $td_x_amount = $td['value'];
                    }

                    if($td['name'] === 'x_response_code'){
                        $td_x_response_code = $td['value'];
                    }

                    if($td['name'] === 'x_response_reason_code'){
                        $td_x_response_reason_code = $td['value'];
                    }

                    if($td['name'] === 'x_response_reason_text'){
                        $td_x_response_reason_text = $td['value'];
                    }
                }

                //--We need to determine if the realm was already looked up or not; if not look it up first!
                $realm_end = preg_replace("/^.+@/", "", $username);
                if (array_key_exists($realm_end, $realm_lookup)) {
                    $realm = $realm_lookup[$realm_end];
                }else{

                    $r = $this->Realm->find('first',array('conditions' => array('Realm.append_string_to_user' => $realm_end)));
                   // print_r($r);
                    $realm_lookup[$realm_end] = $r['Realm']['name'];
                }

                array_push($items,array(
                            'id'                    => $id,
                            'realm'                 => $realm_lookup[$realm_end],
                            'username'              => $username,
                            'amount'                => $td_x_amount,
                            'response_code'         => $td_x_response_code,
                            'response_reason_code'  => $td_x_response_reason_code,
                            'response_reason_text'  => $td_x_response_reason_text,
                            'ip_address'            => $ip,
                            'created'               => $created
                        ));
            }
           // print_r($list);

        }
        $json_return['items']             = $items;

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


    function json_view($id=null){

          $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();                        

        $q_r = $this->TransactionDetail->find('all',array('conditions' => array('TransactionDetail.cc_transaction_id' => $id)));
        foreach($q_r as $i){
            $n = $i['TransactionDetail']['name'];
            $v = $i['TransactionDetail']['value'];
            $i = $i['TransactionDetail']['id'];
            array_push($json_return['items'] , array('id' => $i, 'name' => $n, 'value' => $v));
        }
         //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
        

    }


    function adjustExpiration($cc_trans_id){

        $response_c =   $_POST['x_response_code'];

        if($response_c != 1){   //Failure return without doing anything
            return;
        }

        //First we will get all the values of the required fields
        $amount     =   $_POST['x_amount'];
        $plan       =   $_POST['x_description'];
        $username   =   $_POST['x_cust_id'];
        $time       =   false;
        
        $initiator  = $this->User->findByUsername('CreditCardProvider');
        $ini_id     = $initiator['User']['id'];

        //Find the user's detail for the x_cust_id
        $q_r        = $this->User->findByUsername($username);
        $user_id    = $q_r['User']['id'];
        
        //See if there is a valid plan by combining the plan and amount in a lookup check
        Configure::load('yfi');
        $costing = Configure::read('costing');
        foreach($costing as $i){
            if(($i['plan'] == $plan)&&($i['cost'] == $amount)){
                //Now we can get the time to add...
                $time = $i['time'];
            }
        }

        if($time != false){ //Found a valid combination
            $q_r = $this->Radcheck->find('first',
                    array('conditions' => 
                        array(
                            'username'  => $username,
                            'attribute' => 'Expiration'
            )));
            $current_exp = $q_r['Radcheck']['value'];
            $old_exp     = $current_exp;
            //If it was in the past then start today
            $now = time();
            if($current_exp < $now){
                $current_exp = $now;
            }

            //Add the time value
            $new_exp = $current_exp + $time;
            $q_r['Radcheck']['value'] = $new_exp;
            $this->Radcheck->save($q_r);

            //Add an entry for the expiry_changes
            //Record this change....
            $e_ch = array();
            $e_ch['ExpiryChange']['old_value']          = $old_exp;
            $e_ch['ExpiryChange']['new_value']          = $new_exp;
            $e_ch['ExpiryChange']['user_id']            = $user_id;
            $e_ch['ExpiryChange']['initiator_id']       = $ini_id;
            $e_ch['ExpiryChange']['cc_transaction_id']  = $cc_trans_id;
            $this->ExpiryChange->save($e_ch);

        }


    }


    //*****************************************************************************************************
    //----------- Dynamic Actions -------------------------------------------------------------------------
    //*****************************************************************************************************
 
    function json_actions(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on credit card transactions tab-- 
        //--AP Sepcific: NO --------------------------------------------------------
        //--Rights Completed:   NO -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                     = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_cc_transactions();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }

    function _returnSearchFilterConditions(){
        $conditions = array(); //This will grow in complexity

        return $conditions;
    }

    function _returnOrderClause(){

        $s ='';
        return $s;
    }

}
?>
