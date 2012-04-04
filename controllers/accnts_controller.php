<?
class AccntsController extends AppController {
    var $name       = 'Accnts';

    var $helpers    = array('Javascript');
    var $uses       = array('BillingPlan','BillingPlanRealm','Invoice','User','Radacct','Radusergroup','Radgroupcheck','Payment','ExtraService');   //Tables to check for recent changes
    var $components = array('Session','Dojolayout','Rights','Json','Bookkeeper','SwiftMailer','Pdf');    //Add the locker component


    //------------------------------------------------------------------
    //--- List of rights that can be tweaked ---------------------------
    //------------------------------------------------------------------
    /*
        1.) json_index()
        2.) json_add()
        3.) json_del()
        4.) json_payment_add()
        5.) json_del_payment()
        6.) json_del_invoice()
    */
    //-----------------------------------------------------------------



    function pdf_latest(){

        $this->layout   = 'pdf';
        //$this->layout   = 'ajax';
        $invoices       = array();

        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $user_id = $this->params['url'][$key];
                $invoice_detail = $this->Bookkeeper->invoice_detail_for($user_id);
                if(count($invoice_detail['Invoice']) > 0){     //We silently ignore empty invoices
                    array_push($invoices,$invoice_detail);
                }
                //-------------
            }
        }
        $this->set('pdf_object',$this->Pdf->latest_invoices($invoices));
    }

    function pdf_invoices(){        //This will be called from a user's point of view so the user_id will always be the same

        $this->layout   = 'pdf';
        //$this->layout   = 'ajax';

        $counter = 0;
        $user_id;
        $invoices = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $invoice_id = $this->params['url'][$key];
                array_push($invoices,$invoice_id);
                if($counter == 0){  //Only do the first one to get the user id
                    $qr = $this->Invoice->findById($invoice_id);
                    $user_id = $qr['Invoice']['user_id'];
                }
                $counter ++;
                //-------------
            }
        }
        $invoice_detail = $this->Bookkeeper->invoice_detail_for($user_id);
        $this->set('pdf_object',$this->Pdf->selected_invoices_for_user($invoice_detail,$invoices));
    }

    function pdf_payments(){        //This will be called from a user's point of view so the user_id will always be the same

        $this->layout   = 'pdf';
        //$this->layout   = 'ajax';

        $counter = 0;
        $user_id;
        $payments = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $payment_id = $this->params['url'][$key];
                array_push($payments,$payment_id);
                if($counter == 0){  //Only do the first one to get the user id
                    $qr = $this->Payment->findById($payment_id);
                    $user_id = $qr['Payment']['user_id'];
                }
                $counter ++;
                //-------------
            }
        }
        $payment_detail = $this->Bookkeeper->invoice_detail_for($user_id);
       // print_r($payment_detail);
        $this->set('pdf_object',$this->Pdf->selected_payments_for_user($payment_detail,$payments));
    }



    function json_index(){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'username';
        $json_return['identifier']        = 'id';
        


        $conditions     = $this->_returnSearchFilterConditions();
        $sort           = $this->_returnOrderClause();

        //----Lets get to the correct Page------
        $start =0;
        $count;

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
        $list   = $this->User->find('all',array(
                                                    'conditions' => $conditions
                                                    )
                                                );

        //Required for the QueryDataStore (Dojo)
        $json_return['numRows']    = count($list);

        //Now we filted only the required page
        if(($start != '')&($count != '')){
             $list   = $this->User->find('all',array(
                                                    'conditions'    => $conditions,
                                                    'limit'         => $count,
                                                    'page'          => $page,
                                                    'order'         => $sort 
                                                    )
                                                );

        }

        foreach($list as $item){

        

            //Get the billing plan and last dates invoiced

            //Default values
            $start_date = 'NA';
            $end_date   = 'NA';
            $plan       = 'NA';
            $paid       = '0.00';
            $outstanding= '0.00';

            if(count($item['Invoice']) > 0){
                $start_date = $item['Invoice'][0]['start_date'];
                $end_date = $item['Invoice'][0]['end_date'];
                $pq = $this->BillingPlan->findById($item['Invoice'][0]['billing_plan_id']);
                $plan = $pq['BillingPlan']['name'];
                //Calculate the outstanding
                $sum_of_payments = $this->Bookkeeper->get_payments_up_to($item['User']['id'],date('Y-m-d H:i:s', time()));
                $sum_of_invoices = $this->Bookkeeper->get_invoices_up_to($item['User']['id'],date('Y-m-d H:i:s', time()));
                $outstanding     = $sum_of_invoices - $sum_of_payments;

            }

            if(count($item['Payment']) > 0){
                $when = explode(" ",$item['Payment'][0]['created']);
                $paid = $when[0]." (".$item['Payment'][0]['amount'].")";
            }

            //$this->Bookkeeper->get_outstanding($item['User']['id'],$item['User']['username']);
            array_push($items,array(
                                    'id'            => $item['User']['id'],
                                    'username'      => $item['User']['username'],
                                    'realm'         => $item['Realm']['name'],
                                    'start'         => $start_date,
                                    'end'           => $end_date,
                                    'plan'          => $plan,
                                    'outstanding'   => round($outstanding, 2),
                                    'paid'          => $paid,
                            ));
        }
        $json_return['items']             = $items;

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_send_mail(){

        $this->layout = 'ajax';

        $subject        = $this->params['form']['subject'];
        $message        = $this->params['form']['message'];
        $attach_invoice = false;
        if(array_key_exists('attach_invoice',$this->params['form'])){  //VPN
            $attach_invoice = true;
        }

        Configure::load('yfi');
        $auth_data = $this->Session->read('AuthInfo');
        $from_email = $auth_data['User']['email'];
        if($from_email == ''){
            $from_email = Configure::read('email.from');
        }

        $invoices       = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $user_id = $this->params['url'][$key];
                $invoice_detail = $this->Bookkeeper->invoice_detail_for($user_id);
                if(count($invoice_detail['Invoice']) > 0){     //We silently ignore empty invoices
                    array_push($invoices,$invoice_detail);
                }
                //-------------
            }
        }
        //If the attach_invoice is NOT set, we can just send a batch-email out
        if(!$attach_invoice){

            $to_list = array();
            foreach($invoices as $item){

                $mail = $item['User']['email'];
                $pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
                            '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';

                if(preg_match ($pattern, $mail)){
                    array_push($to_list,$mail);
                }

            }
            //print_r($to_list);
            //Prepare and send message
            $this->SwiftMailer->sendMessage($to_list,$from_email,$subject,$message);

        }else{

            //Get the last invoice of each user - then write that to a file and mail it as  an attachment
            //We need to do the invoices one by one!
            foreach($invoices as $invoice){
                $pdf_structure = array();
                array_push($pdf_structure, $invoice);
                $pdf_object = $this->Pdf->latest_invoices($pdf_structure);
                $filename = "/tmp/".$this->_generateFilename().".pdf";
                //Create the file 
                $pdf_object->Output($filename,'F');
                //Mail it as an attachment
                $to = $invoice['User']['email'];
                $pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
                            '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';

                if(preg_match ($pattern, $to)){
                    $this->SwiftMailer->sendAttachment($to,$from_email,$subject,$message,$filename);
                    //Delete the temp file
                    unlink($filename);
                }
            }
        }
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_send_mail_invoices(){

        $this->layout = 'ajax';

        $subject        = $this->params['form']['subject'];
        $message        = $this->params['form']['message'];
        $attach_invoice = false;
        if(array_key_exists('attach_invoice',$this->params['form'])){  //VPN
            $attach_invoice = true;
        }

        Configure::load('yfi');
        $auth_data = $this->Session->read('AuthInfo');
        $from_email = $auth_data['User']['email'];
        if($from_email == ''){
            $from_email = Configure::read('email.from');
        }

        $counter = 0;
        $user_id;
        $invoices = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $invoice_id = $this->params['url'][$key];
                array_push($invoices,$invoice_id);
                if($counter == 0){  //Only do the first one to get the user id
                    $qr = $this->Invoice->findById($invoice_id);
                    $user_id = $qr['Invoice']['user_id'];
                }
                $counter ++;
                //-------------
            }
        }
        $invoice_detail = $this->Bookkeeper->invoice_detail_for($user_id);
        $to_list        = array($invoice_detail['User']['email']);
        //If the attach_invoice is NOT set, we can just send a batch-email out
        if(!$attach_invoice){
            //print_r($to_list);
            //Prepare and send message
            $this->SwiftMailer->sendMessage($to_list,$from_email,$subject,$message);

        }else{

            $pdf_object = $this->Pdf->selected_invoices_for_user($invoice_detail,$invoices);
            $filename = "/tmp/".$this->_generateFilename().".pdf";
            //Create the file 
            $pdf_object->Output($filename,'F');
            $this->SwiftMailer->sendAttachment($to_list,$from_email,$subject,$message,$filename);
            //Delete the temp file
            unlink($filename);
        }


        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

     function json_send_mail_payments(){

        $this->layout = 'ajax';

        $subject        = $this->params['form']['subject'];
        $message        = $this->params['form']['message'];
        $attach_payment = false;
        if(array_key_exists('attach_payment',$this->params['form'])){  //VPN
            $attach_payment = true;
        }

        Configure::load('yfi');
        $auth_data = $this->Session->read('AuthInfo');
        $from_email = $auth_data['User']['email'];
        if($from_email == ''){
            $from_email = Configure::read('email.from');
        }

        $counter = 0;
        $user_id;
        $payments = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $payment_id = $this->params['url'][$key];
                array_push($payments,$payment_id);
                if($counter == 0){  //Only do the first one to get the user id
                    $qr = $this->Payment->findById($payment_id);
                    $user_id = $qr['Payment']['user_id'];
                }
                $counter ++;
                //-------------
            }
        }

        $payment_detail = $this->Bookkeeper->invoice_detail_for($user_id);
        $to_list        = array($payment_detail['User']['email']);
        //If the attach_invoice is NOT set, we can just send a batch-email out
        if(!$attach_payment){
            //print_r($to_list);
            //Prepare and send message
            $this->SwiftMailer->sendMessage($to_list,$from_email,$subject,$message);

        }else{
            // print_r($payment_detail);
            $pdf_object = $this->Pdf->selected_payments_for_user($payment_detail,$payments);
            $filename = "/tmp/".$this->_generateFilename().".pdf";
            //Create the file 
            $pdf_object->Output($filename,'F');
            $this->SwiftMailer->sendAttachment($to_list,$from_email,$subject,$message,$filename);
            //Delete the temp file
            unlink($filename);
        }
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_add(){
        $this->layout = 'ajax';

        $start          =   $this->params['form']['start_date'];
        $end            =   $this->params['form']['end_date'];
        $billing_plan   =   $this->params['form']['billing_plan'];

        $d                                 = array();
        $d['Invoice']['id']                = '';
        $d['Invoice']['billing_plan_id']   = $billing_plan;
        $d['Invoice']['start_date']        = $start;
        $d['Invoice']['end_date']          = $end." 23:59:59";

        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $user_id = $this->params['url'][$key];
                $d['Invoice']['user_id']    = $user_id;
                $this->Invoice->save($d);
                $this->Invoice->id          = false;
                //-------------
            }
        }
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_del(){

        $this->layout = 'ajax';

         foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $user_id = $this->params['url'][$key];
                //Get the last entry in the invoices table
                $q_r = $this->Invoice->find('first',array('conditions' => array('Invoice.user_id' => $user_id),'order' => array('Invoice.end_date DESC')));
                if($q_r != ''){
                    $this->Invoice->delete($q_r['Invoice']['id']);
                }
                //-------------
            }
        }

        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);

    }

     function json_payment_add(){
        $this->layout = 'ajax';

        $amount          =   $this->params['form']['amount'];
        $received_date   =   $this->params['form']['received_date'];

        $d                                 = array();
        $d['Payment']['id']                = '';
        $d['Payment']['amount']            = $amount;
        $d['Payment']['created']           = $received_date;

        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $user_id = $this->params['url'][$key];
                $d['Payment']['user_id']    = $user_id;
                //--Get the outstanding balance -----
                if(array_key_exists('all_outstanding',$this->params['form'])){  //Normal
                    $userdetail = $this->Bookkeeper->invoice_detail_for($user_id);
                    $invoice_count = count($userdetail['Invoice']);
                    if($invoice_count > 0){
                        $last_invoice = $userdetail['Invoice'][($invoice_count-1)]['Stats']['outstanding']; //Should we change this?
                        $d['Payment']['amount']   = $last_invoice;
                    }
                }
                $this->Payment->save($d);
                $this->Payment->id          = false;
                //-------------
            }
        }
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

    function json_invoices_for($id){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';

        $items                            = array();

        $userdetail = $this->Bookkeeper->invoice_detail_for($id);
       // print_r($userdetail);
        //Swap the reversed (to calculate totals) back

        foreach(array_reverse($userdetail['Invoice']) as $invoice){
             array_push($items,array(
                                    'id'            => $invoice['id'],
                                    'start_date'    => $invoice['start_date'],
                                    'end_date'      => $invoice['end_date'],
                                    'billing_plan'  => $invoice['BillingPlan']['name'],
                                    'total'         => $invoice['Payable']['total_with_tax'],
                                    'invoice_sum'   => round($invoice['Stats']['invoice_sum'],2),
                                    'payment_sum'   => round($invoice['Stats']['payment_sum'],2),
                                    'outstanding'   => round($invoice['Stats']['outstanding'],2)
                            ));
        }

        $json_return['items']             = $items;

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }


     function json_payments_for($id){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';

        $items                            = array();

        

        $userdetail = $this->Bookkeeper->invoice_detail_for($id);
       // print_r($userdetail);
        //Swap the reversed (to calculate totals) back

        foreach($userdetail['Payment'] as $payment){
            $sum_of_payments = $this->Bookkeeper->get_payments_up_to($id,$payment['created']);
            $sum_of_invoices = $this->Bookkeeper->get_invoices_up_to($id,$payment['created']);
             array_push($items,array(
                                    'id'            => $payment['id'],
                                    'date'          => $payment['created'],
                                    'amount'        => $payment['amount'],
                                    'invoice_sum'   => round($sum_of_invoices, 2),
                                    'payment_sum'   => round($sum_of_payments, 2),
                                    'outstanding'   => round(($sum_of_invoices-$sum_of_payments), 2),
                            ));
        }


        $json_return['items']             = $items;

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }

    function json_del_payment(){

        $this->layout = 'ajax';

         foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $id = $this->params['url'][$key];
                $this->Payment->delete($id);
                //-------------
            }
        }
        $json_return= array();
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

     function json_del_invoice(){

        $this->layout = 'ajax';

         foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $id = $this->params['url'][$key];
                $this->Invoice->delete($id);
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
        //--Description: Get the actions to display on templates tab----------------- 
        //--AP Sepcific: YES --------------------------------------------------------
        //--Rights Completed:   NA -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                     = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_accnts();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_actions_invoices(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on templates tab----------------- 
        //--AP Sepcific: YES --------------------------------------------------------
        //--Rights Completed:   NA -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                     = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_accnt_invoices();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_actions_payments(){
        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on templates tab----------------- 
        //--AP Sepcific: YES --------------------------------------------------------
        //--Rights Completed:   NA -------------------------------------------------
        //----------------------------------------------------------------------------
        $this->layout                     = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_accnt_payments();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }




      function _returnSearchFilterConditions(){

        //----------------Search Filter ----------------------
        $column;
        $condition;
        if(array_key_exists('username',$this->params['url'])){
            $column    = 'User.username';
            $condition  = $this->params['url']['username'];
        }

        if(array_key_exists('realm',$this->params['url'])){
            $column    = 'Realm.name';
            $condition  = $this->params['url']['realm'];
        }
         //SQL-aaize it
        $condition  = preg_replace( '/\*/', '%', $condition);
        

        $conditions = array(); //This will grow in complexity
        array_push($conditions,array("$column LIKE" => "$condition")); //Add This AND filtertjie

        //----Special Clauses for AP's ---------------------
        Configure::load('yfi');
        $auth_info = $this->Session->read('AuthInfo');
        //--Realms only need to be checked only for Access Providers--
        if($auth_info['Group']['name'] == Configure::read('group.ap')){   //They can only see whet the are permitted to see 

            //Access Providers should have a list of Realms
            //Check if there are realms assinged to this user and then build the query form it.
            if(!empty($auth_info['Realms'])){
                $realm_filter = array();
                foreach($auth_info['Realms'] as $realm_line){
                    $name_ends_with = $realm_line['append_string_to_user'];
                    array_push($realm_filter,array("User.username LIKE" => '%@'.$name_ends_with));
                }
            }

            array_push($conditions,array('or' => $realm_filter));

            //--------------------------
            //Access Providers will by default only view users created by them
            //This makes it nice for branches eg an AP is assigned to a branch and only manages their users
            //A Manager then can view all users inside a realm
            //**PERMISSION 'users/only_view_own'
            //**FUNCTION Only list the users an Access Provider created them self
            if($this->_look_for_right('permanent_users/only_view_own')){       #FIXME Change to users....
                    $user_id = $auth_info['User']['id'];
                    array_push($conditions,array("User.user_id" => $user_id)); //Add This AND filtertjie
            }
        };
        //---- We only list group type 'users'-----------------
        Configure::load('yfi');
        $user_name = Configure::read('group.user');
        array_push($conditions,array("Group.name" => $user_name));

		//---- Prepaid users are not listed in accounts
		array_push($conditions,array("User.cap <>" => 'prepaid'));

        //-------------END Search Filter --------------------------------
        return $conditions;
    }

     function _returnOrderClause(){

         //-----------Order Clause---------------------------------------
        $s ='';
        $sord_order;
        if(array_key_exists('sort',$this->params['url'])){  //The sort option is not always present
            $sort       = $this->params['url']['sort'];
            //Check if it is ASCENDING or DESC
            if(preg_match('/^-.+/', $sort)){
                $sort_order = 'DESC';
            }else{
                $sort_order = 'ASC';
            }

            if(preg_match('/username/',$sort)){

                $s = "User.username $sort_order";
            }

            if(preg_match('/realm/',$sort)){

                $s = "Realm.name $sort_order";
            }
        }
        //-------END Order Clause---------------------------------------
        return $s;
    }

      function _look_for_right($right){

        $auth_data = $this->Session->read('AuthInfo');
        if(array_key_exists($right,$auth_data['Rights'])){

            if($auth_data['Rights'][$right]['state'] == '1'){
                return true;
            }
            return false;
        }
        return false;   //Default
    }


    //Generates a unique fileneame
    function _generateFilename ($length = 8){

        // start with a blank password
        $password = "";
        // define possible characters
       // $possible = "!#$%^&*()+=?0123456789bBcCdDfFgGhHjJkmnNpPqQrRstTvwxyz";
        $possible = "0123456789bBcCdDfFgGhHjJkmnNpPqQrRstTvwxyz";
        // set up a counter
        $i = 0; 
        // add random characters to $password until $length is reached
        while ($i < $length) { 

            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
            // we don't want this character if it's already in the password
            if (!strstr($password, $char)) { 
                $password .= $char;
                $i++;
            }
        }
        // done!
        return $password;
    }

}
?>