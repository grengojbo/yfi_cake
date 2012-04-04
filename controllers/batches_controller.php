<?
class BatchesController extends AppController {
    var $name       = 'Batches';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights');    //Add the locker component
    var $uses       = array('Batch','Voucher','Realm','Radcheck','Radusergroup','Radreply','Radgroupcheck','Radgroupreply');

   // var $scaffold;

    //------------------------------------------------------------------
    //--- List of rights that can be tweaked ---------------------------
    //------------------------------------------------------------------
    /*
        1.) csv
        2.) pdf
        3.) json_index
        4.) json_del
        5.) json_view
    */
    //-----------------------------------------------------------------

    function csv(){

        $this->layout = 'csv';

        //Get the list of selected voucher id's
        $batch_list = array();
        foreach(array_keys($this->params['url'])as $key){

            if(preg_match('/^\d/',$key)){
               array_push($batch_list,$this->params['url'][$key]);
            }
        }

        $voucher_detail = array();
        $counter = 0;
        foreach($batch_list as $batch_id){
            $results = $this->Batch->findById($batch_id);
            foreach($results['Voucher'] as $line){

                $id             = $line['id'];
                $qr             = $this->Voucher->findById($id);
                $username       = $qr['Radcheck']['username'];
                $profile        = $qr['Profile']['name'];
                $realm          = $qr['Realm']['name'];
                $voucher_detail[$counter]   = $this->_get_voucher_detail($username);
                $voucher_detail[$counter]['realm']     = $realm;
                $voucher_detail[$counter]['profile']   = $profile;
                $voucher_detail[$counter]['username']  = $username;
                $counter++;
            }
        }
        $this->set('csv_structure',$voucher_detail);
    }

    function pdf(){

        //___Change the language___
        Configure::load('yfi');
        $locale_location       = Configure::read('locale.location');
        $q_r  = $this->Language->findById($this->params['url']["language"]);
        $iso_name = $q_r['Language']['iso_name'];
        putenv("LANG=$iso_name.utf8"); 
        putenv("LANGUAGE=$iso_name.utf8"); 
        //setlocale(LC_ALL, "$iso_name.utf8");
        setlocale(LC_MESSAGES, "$iso_name.utf8");
        setlocale(LC_NUMERIC, "en");
        // Set the text domain as 'messages'
        $domain = 'messages';
        bindtextdomain($domain,$locale_location); 
        textdomain($domain);
        //_______________________________

        $this->layout = 'pdf';

        $format  = $this->params['url']["pdf_format"];
        $this->set('format',$format);

        //Get the list of selected voucher id's
        $batch_list = array();
        foreach(array_keys($this->params['url'])as $key){

            if(preg_match('/^\d/',$key)){
               array_push($batch_list,$this->params['url'][$key]);
            }
        }

        if(preg_match('/generic/',$format)){
            $generic_structure = $this->_get_generic_pdf_data($batch_list);
            $this->set('pdf_structure',$generic_structure);

        }else{

            $counter = 0;
            foreach($batch_list as $batch_id){
                $results = $this->Batch->findById($batch_id);
                foreach($results['Voucher'] as $line){

                    $id             = $line['id'];
                    $qr             = $this->Voucher->findById($id);
                    $username       = $qr['Radcheck']['username'];
                    $profile        = $qr['Profile']['name'];
                    $icon           = $qr['Realm']['icon_file_name'];
                    $voucher_detail[$counter]   = $this->_get_voucher_detail($username);
                    $voucher_detail[$counter]['icon']      = $icon;
                    $voucher_detail[$counter]['profile']   = $profile;
                    $voucher_detail[$counter]['username']  = $username;
                    $counter++;
                }
            }
            $this->set('pdf_structure',$voucher_detail);
        }
    }



    function json_index(){


        $this->layout = 'ajax';

        //Check who we are
        $auth_info = $this->Session->read('AuthInfo');
        if(empty($auth_info)){  //No Login -no Display
            return;
        }
        

        //---Determine if we are an Administrator/ Access Provider or User---
        $group = $auth_info['Group']['name'];

        if($group == 'Users'){  //They are not allowed to see anything

            $this->set('json_return',$this->Json->permAuthListEmpty());
            return;
        }

        //=====================================
        $conditions = array();
        if($group == 'Access Providers'){   //They can only see whet the are permitted to see
            //Access Providers should have a list of Realms
            //Check if there are realms assinged to this user and then build the query form it.
            if(!empty($auth_info['Realms'])){
                $realm_filter = array();
                foreach($auth_info['Realms'] as $realm_line){
                    $name_ends_with = $realm_line['name'];
                    array_push($realm_filter,array("Realm.name" => $name_ends_with));
                }
            }
            array_push($conditions,array('or' => $realm_filter));
        };
        //=======================================

        //We asumme the other group is the 'Administrators' group - which made it pas the above if's and should see everything

        $r = $this->Batch->find('all',array('recursive' => 1,'fields'=>array('Batch.id','Batch.name','Batch.created','Realm.name'),'conditions' => $conditions));
        //print_r($r);

        $json_return = array();
        $json_return['label']      = 'name';
        $json_return['identifier'] = 'id';
        $json_return['items']      = array();

        //----Special Clauses for AP's ---------------------
        //Read the config data
        Configure::load('yfi');
        $ap_only_view_own  = false;
        if($auth_info['Group']['name'] == Configure::read('group.ap')){   //They can only see whet the are permitted to see
            if($this->_look_for_right('vouchers/only_view_own')){
                $ap_only_view_own  = true;
            }
        }
        //-------------END Search Filter --------------------------------



        foreach($r as $entry){
            //print_r($entry);
            $id         = $entry['Batch']['id'];
            $name       = $entry['Batch']['name'];
            $created    = $entry['Batch']['created'];
            $realm      = $entry['Realm']['name'];
            $size       = count($entry['Voucher']);

             
            //--------------------------
            //Access Providers will by default only view vouchers created by them
            //This makes it nice for branches eg an AP is assigned to a branch and only manages their vouchers
            //A Manager then can view all vouchers inside a realm
            //**PERMISSION 'vouchers/only_view_own'
            //**FUNCTION Only list the vouchers an Access Provider created them self
            if($ap_only_view_own){
                if($size > 0){  //Show non-empty belonging to that user
                    $first_voucher_id = $entry['Voucher'][0]['id'];
                    $qr =$this->Voucher->findById($first_voucher_id);
                    if($qr['User']['id'] == $auth_info['User']['id']){
                        array_push($json_return['items'],array('id'=> $id,'name' => $name,'realm' =>$realm, 'size' =>$size,'created'=>$created));
                    }
                }else{
                   // array_push($json_return['items'],array('id'=> $id,'name' => $name,'realm' =>$realm, 'size' =>$size,'created'=>$created));   //They can see empty batches 
                }
            }else{
                array_push($json_return['items'],array('id'=> $id,'name' => $name,'realm' =>$realm, 'size' =>$size,'created'=>$created));
            }
        }
        $this->set('json_return',$json_return);
    }


     function json_del(){

        $this->layout   = 'ajax';

        $json_return = array();
        $json_return['json']['status'] = 'ok';

        //Get a list of batches which has to be deleted
        foreach(array_keys($this->params['url'])as $key){

            if(preg_match('/^\d/',$key)){
                //print "Assume an entry<br>";
                $id = $this->params['url']["$key"];
                $b              = $this->Batch->find('first',array('fields'=>array('Batch.id'),'conditions'=> array('Batch.id' => $id)));
                if($b){
                    $this->Batch->del($id,true);
              //  print_r($b);
                    foreach($b['Voucher'] as $voucher){

                        $id             = $voucher['id'];
                        $v              = $this->Voucher->find('first',array('conditions'=> array('Voucher.id' => $id)));
                        $voucher_name   = $v['Radcheck']['username'];
                        $this->Voucher->del($id,true);
                        $this->Radcheck->deleteAll(     array("username"=>"$voucher_name"),false);
                        $this->Radreply->deleteAll(     array("username"=>"$voucher_name"),true);
                        $this->Radusergroup->removeUser($voucher_name);
                    }
                }
            }
        }

        $this->set('json_return',$json_return);
    }


     function json_view($id=null){

        $this->layout = 'ajax';

        $conditions = array('Batch.id' => $id);

        $json_return = array();
        $json_return['label']      = 'username';
        $json_return['identifier'] = 'id';
        $json_return['items']      = array();


        $results = $this->Batch->find('first',array('conditions'=>$conditions));
        
        //Pull in the list of Vouchers
        foreach($results['Voucher'] as $line){

            $id             = $line['id'];
            $voucher_detail = $this->Voucher->find('first',array('conditions'=>array('Voucher.id' => $id)));
                $creator    = $voucher_detail['User']['username'];
                $username   = $voucher_detail['Radcheck']['username'];
                $password   = $voucher_detail['Radcheck']['value'];
                $created    = $voucher_detail['Voucher']['created'];
                $realm      = $voucher_detail['Realm']['name'];
                $status     = $voucher_detail['Voucher']['status'];
                $profile    = $voucher_detail['Profile']['name'];

            array_push($json_return['items'],array('id'=> $id,'profile' => $profile, 'creator'=>$creator,'username' => $username, 'password' => $password, 'realm' => $realm,'created'=>$created,'status' =>$status));

        }
        $this->set('json_return',$json_return);
    }


    function _get_generic_pdf_data($batch_list){

        $pdf_structure = array();

        foreach($batch_list as $batch_id){
            $results = $this->Batch->findById($batch_id);
            foreach($results['Voucher'] as $line){

                $id             = $line['id'];
                $qr = $this->Voucher->findById($id);
                $realm_name     = $qr['Realm']['name'];
                $profile_name   = $qr['Profile']['name'];
                $voucher_name   = $qr['Radcheck']['username'];

                if(!array_key_exists($realm_name,$pdf_structure)){
                    $pdf_structure[$realm_name] = array();
                }
                //Prime the 'detail' for the Realm
                if(!array_key_exists ('detail',$pdf_structure[$realm_name])){

                    $pdf_structure[$realm_name]['detail'] = $qr['Realm'];
                }

                //Prime the 'profiles' for the Realm
                if(!array_key_exists ('profiles',$pdf_structure[$realm_name])){
                    $pdf_structure[$realm_name]['profiles']= array();
                }

                if(!array_key_exists($profile_name,$pdf_structure[$realm_name]['profiles'])){

                    $pdf_structure[$realm_name]['profiles'][$profile_name]['detail']    = $this->_profile_attributes($profile_name);
                }
                $pdf_structure[$realm_name]['profiles'][$profile_name]['vouchers'][$voucher_name] = $this->_get_voucher_detail($voucher_name);
            }
        }

        return $pdf_structure;
    }

    function _profile_attributes($profile_name){

        $profile_attributes = array();
        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupcheck->find('all',array('conditions' => array('Radgroupcheck.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupcheck']['attribute'];
            $value      = $item['Radgroupcheck']['value'];
            array_push($profile_attributes,array('attribute' => $attribute, 'value' => $value));
        }

        //Get all the radgroucheck and radgroupreply for this profile
        $qr = $this->Radgroupreply->find('all',array('conditions' => array('Radgroupreply.groupname'=> $profile_name)));
        foreach($qr as $item){
            
            $attribute  = $item['Radgroupreply']['attribute'];
            $value      = $item['Radgroupreply']['value'];
            array_push($profile_attributes,array('attribute' => $attribute, 'value' => $value));
        }

        return $profile_attributes;
    }

    function _get_voucher_detail($voucher_name){

        $voucher_detail = array();

        $qc = $this->Radcheck->findAllByUsername($voucher_name);
        foreach($qc as $item){

            if($item['Radcheck']['attribute'] == 'Cleartext-Password'){
                $voucher_detail['password'] = $item['Radcheck']['value'];
            }

            if($item['Radcheck']['attribute'] == 'Yfi-Voucher'){
                $valid                          = $item['Radcheck']['value'];
                $pieces                         = explode('-',$valid);
                //$voucher_detail['days_valid']   = $pieces[0].' Days '.$pieces[1].' Hours '.$pieces[2].' Minutes ';
                //Change this on suggestion of user
                if($pieces[0] != 0){
                    if($pieces[0] >= 2){
                        $voucher_detail['days_valid']   = $pieces[0].' '.gettext('days from first log-in');
                    }else{
                        $voucher_detail['days_valid']   = $pieces[0].' '.gettext('day from first log-in');
                    }
                }
            }
        }

        $qr = $this->Radreply->findAllByUsername($voucher_name);
        foreach($qr as $item){
            if($item['Radreply']['attribute'] == 'WISPr-Session-Terminate-Time'){
                $term_time  = $item['Radreply']['value'];
                $pieces      = explode('T',$term_time);
                $voucher_detail['expiry_date'] = $pieces[0];
            }
        }

        return $voucher_detail;
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

        $json_return['items']             = $this->Dojolayout->actions_for_batches();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_actions_view(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on a profile view tab------------ 
        //--AP Sepcific: YES ---------------------------------------------------------
        //--Rights Completed:   NA ---------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_batch_view();
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


}
?>
