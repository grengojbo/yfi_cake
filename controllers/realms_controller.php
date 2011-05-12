<?
class RealmsController extends AppController {
    var $name       = 'Realms';
    var $helpers    = array('Javascript');
    var $uses       = array('Realm','UserRealm','Check','Radacct');

    var $components = array('Session','Rights','Json','Freeradius','Formatter');    //Add the locker component

    //var $scaffold;

    function json_index(){  //For the dojo Grid
    //-------------------------------------------------------------------
    //---- Admins   => list all the realms ------------------------------
    //--- APs        => Only realms they are assigned to ----------------
    //---Users       => No access----------------------------------------
    //-------------------------------------------------------------------

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //-- Query the DB ---------------------------------------------
        $r = $this->Realm->find(
                            'all',
                            array(
                                'conditions'=> $this->Rights->GetRealmClause(),
                                'fields'=>array('Realm.name','Realm.append_string_to_user','Realm.id','Realm.phone','Realm.cell','Realm.email')
                            )
            );
        foreach($r as $entry){
            array_push($json_return['items'],$entry['Realm']);
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

     function json_stats(){  //For the dojo Grid
    //-------------------------------------------------------------------
    //---- Admins   => list all the realms ------------------------------
    //--- APs        => Only realms they are assigned to ----------------
    //---Users       => No access----------------------------------------
    //-------------------------------------------------------------------

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //__________Dummy Dates___________________________
        //$start_date = '2009-10-01 00:00:00';
        //$end_date   = '2009-10-31 23:59:59';
        $start_date = date ("Y-m-d H:i:s", $this->params['url']['sd']);
        $end_date   = date ("Y-m-d H:i:s", ($this->params['url']['ed']+(60*60*24)-1)); //Go to the end of the day
        //________ END Dummy Dates _______________________

        //-- Query the DB ---------------------------------------------
        $r = $this->Realm->find(
                            'all',
                            array(
                                'conditions'=> $this->Rights->GetRealmClause(),
                                'fields'=>array('Realm.name','Realm.append_string_to_user','Realm.id')
                            )
            );
        foreach($r as $entry){

            $realm_acct_name    = $entry['Realm']['append_string_to_user'];
            $usage              = $this->_get_realm_usage($realm_acct_name,$start_date,$end_date);
            $usage['name']      = $entry['Realm']['name'];
            $usage['id']        = $entry['Realm']['id'];
            array_push($json_return['items'],$usage);
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


     function json_index_list(){    //For the dojo List

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok';
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //-- Query the DB ---------------------------------------------
        $r = $this->Realm->find(
                            'all',
                            array(
                                'conditions'=> $this->Rights->GetRealmClause(),
                                'fields'=>array('Realm.name', 'Realm.id'),
                                'order' => 'Realm.name ASC',
                            )
            );

        $count = 0;
        foreach($r as $entry){
            if($count == 0){
                array_push($json_return['items'],array('name' => $entry['Realm']['name'], 'id' => $entry['Realm']['id'], 'selected' => 'selected')); //Select the first one
            }else{
                array_push($json_return['items'],array('name' => $entry['Realm']['name'], 'id' => $entry['Realm']['id'], 'selected' => ''));
            }
            $count ++;
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_realms_for_ap($userId){
    //------------------------------------------
    //---TODO STILL NEEDS FINISHING!!!!!!!------
    //------------------------------------------

         $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok';
        $json_return['items']              = array();
        //-----------------------------------------

        //-- Query the DB ---------------------------------------------
        $r_all = $this->Realm->find(
                            'all',
                            array(
                                'conditions'=> $this->Rights->GetRealmClause(),
                                'fields'=>array('Realm.name', 'Realm.id'),
                                'order' => 'Realm.name ASC',
                            )
            );


        foreach($r_all as $entry){

            //Check if there is an entry in the UserRealm table for this user
            $count = $this->UserRealm->find('count', array('conditions' => array('UserRealm.user_id' => $userId,'UserRealm.realm_id' => $entry['Realm']['id'])));
            if($count){
                array_push($json_return['items'],array('name' => $entry['Realm']['name'], 'id' => $entry['Realm']['id'], 'selected' => 'selected')); //Select the first one
            }else{
                array_push($json_return['items'],array('name' => $entry['Realm']['name'], 'id' => $entry['Realm']['id'], 'selected' => ''));
            }
        }
        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }

      function json_add(){

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        $this->data = array();
        $this->data['Realm']['id']              = "";
        $this->data['Realm']['name']            = $this->params['form']['Name'];
        $this->data['Realm']['append_string_to_user'] = $this->params['form']['Append'];
        $this->data['Realm']['phone']           = $this->params['form']['Phone'];
        $this->data['Realm']['fax']             = $this->params['form']['Fax'];
        $this->data['Realm']['cell']            = $this->params['form']['Cell'];
        $this->data['Realm']['email']           = $this->params['form']['Email'];
        $this->data['Realm']['url']             = $this->params['form']['Url'];
        $this->data['Realm']['address']         = $this->params['form']['Address'];

        $this->Realm->save($this->data);

        $json_return= array();
        $json_return['Realm']['id']    = $this->Realm->id;
        //Add the realm to the realms
        $this->Freeradius->realm_add($this->params['form']['Append']);

        //---END Temp-------

       // $json_return['Realm']['name']  = $this->data['Realm']['name'];
      //  $json_return['Realm']['append']= $this->data['Realm']['append_string_to_user'];
        $json_return['json']['status'] = "ok";
        $this->set('json_return',$json_return);
    }

     function json_delete(){

        $this->layout = 'ajax';

         //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //------------------------------------

        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){

                //-------------
                $q_r = $this->Realm->findById($this->params['url'][$key]);
                if($q_r){
                    $append = $q_r['Realm']['append_string_to_user'];
                    $this->Freeradius->realm_del($append);
                }
                //-------------

                $this->Realm->del($this->params['url'][$key],true);
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_view($id=null){

        $this->layout = 'ajax';

        //--------Check the rights------
        if(!$this->Rights->CheckRights()){  //If it fails we return the fail JSON
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        //-------------------------------------

        if($this->Rights->CheckRealmIdAllowed($id)){    //Are this user tied to this realm (can they view it)
            $json_return = $this->Realm->find('first',array('conditions'=> array('Realm.id' => $id),'recursive'=> 0));
            $json_return['json']['status'] = 'ok';
            $this->set('json_return',$json_return);
        }else{
            $this->set('json_return',$this->Json->permFail());
        }
    }

    function json_edit(){

        $this->layout = 'ajax';
        $d['Realm']['id']                   = $this->params['form']['id'];
        $d['Realm']['name']                 = $this->params['form']['Name'];
        $d['Realm']['append_string_to_user']= $this->params['form']['Append'];
        $d['Realm']['phone']                = $this->params['form']['Phone'];
        $d['Realm']['fax']                  = $this->params['form']['Fax'];
        $d['Realm']['cell']                 = $this->params['form']['Cell'];
        $d['Realm']['email']                = $this->params['form']['Email'];
        $d['Realm']['url']                  = $this->params['form']['Url'];
        $d['Realm']['address']              = $this->params['form']['Address'];

        //If the append_string_to_user have changed, we remove the old one and replace it
        $q_r = $this->Realm->findById($this->params['form']['id']);
        if($q_r){

            $old_name   = $q_r['Realm']['append_string_to_user'];
            $new_name   = $this->params['form']['Append'];
            $this->Freeradius->realm_del($old_name);
            $this->Freeradius->realm_add($new_name);
        }
        $this->Realm->save($d);
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function json_logo_for_realm($realmId){
        
        $this->layout = 'ajax';

        $q_r = $this->Realm->findById($realmId);
        

        $json_return['json']['status']      = 'ok';
        $json_return['logo']['file_name']    = $q_r['Realm']['icon_file_name'];
        
        $this->set('json_return',$json_return);

    }

    function json_upload_image($realm_id){

        $this->layout = 'ajax';

        
        $filename   = 'fileToUpload'.$realm_id;
        $file_temp  = $_FILES[$filename]['tmp_name'];
        $name       = $_FILES[$filename]['name'];
        $extension  = $_FILES[$filename]['type'];
        $extension  = preg_replace('/.+\//','',$extension);
        Configure::load('yfi');
        $directory  = Configure::read('realm.icon_directory');

        $filename   = $realm_id.'.'.$extension;
        $new_file   = $directory.$filename;

        exec("/usr/bin/mogrify -resize x50 $file_temp");
        exec("cp $file_temp $new_file");
        exec("chmod 644 $new_file");

        $d['Realm']['id']                   = $realm_id;
        $d['Realm']['icon_file_name']       = $filename;
        $this->Realm->save($d);
        
        $json_return['json']['status']      = 'ok';
        
        //$json_return['json']['file']        = $_FILES[$filename];
        //$json_return['image']['id']         = $new_id;
        $json_return['image']['file']       = $filename;
        $json_return['image']['name']       = $name;
        
        $this->set('json_return',$json_return);

    }


     function json_restart_chk(){

        $this->layout = 'ajax';

        // Check the back-off interval
        Configure::load('yfi');
        $back_off = Configure::read('freeradius.back_off_minutes');

        $q_r =$this->Check->find('first',array('conditions' => array('Check.name' =>'radius_restart')));
        $restarted;

        if($q_r){

            $restarted   = $q_r['Check']['modified'];

        }else{

            //Not yet restarted VIA CRON script
            $json_return['restart_wait']    = true;
            $json_return['json']['status']  = "ok";
            $this->set('json_return',$json_return);
            return;
        }

        //Get a list of Realms
        $r = $this->Realm->find('all',array()); //Get all the Realms
        $restart_flag = false;

        foreach($r as $entry){

            $modified   = $entry['Realm']['modified'];
            if(strtotime($restarted) < strtotime($modified)){
                $restart_flag = true;
                 //------------------------------------------
                    $last_plus_cool_off = strtotime($restarted)+ ($back_off * 60);
                    $dateTime       = new DateTime("now");
                    $date_now       = $dateTime->format("Y-m-d H:i:s"); 
                    $now            = strtotime($date_now);
                    $clear          = $now-$last_plus_cool_off;
                    if($clear > 0){

                         //---Use a session variable to count down---
                        if($this->Session->check('Realms.restart')){
                            $initial = $this->Session->read('Realms.restart');
                            $json_return['restart_countdown']    = $this->Formatter->_sec2hms(abs($initial - $now));
                        }else{
                            $this->Session->write('Realms.restart',($now+300));
                            $json_return['restart_countdown']    = $this->Formatter->_sec2hms(300);    //Start with 5 minutes
                        }
                        //----------------------------------------

                    }else{
                        $time = $this->Formatter->_sec2hms(abs($clear));
                        $json_return['restart_countdown']    = $time;       //Count down for cron
                    }

                //-----------------------------------------
                break;
            }
        }

        $json_return['restart_wait']    = $restart_flag;
        $json_return['json']['status']  = "ok";
        $this->set('json_return',$json_return);

    }

    function _get_realm_usage($realm,$start_date,$end_date){

        $query = "SELECT SUM(acctinputoctets) AS total_input,SUM(acctoutputoctets) AS total_output FROM radacct AS Radacct where realm='".$realm."' AND acctstarttime >='".$start_date."' AND acctstoptime <='".$end_date."'";

        $qr = $this->Radacct->query($query);
        $total_input = $total_output = $total_time = 0;
        //print_r($qr);
        ($qr[0][0]['total_input']   != '')  &&  ($total_input    = $qr[0][0]['total_input']);
        ($qr[0][0]['total_output']  != '')  &&  ($total_output   = $qr[0][0]['total_output']);

        $query = "SELECT COUNT(DISTINCT username) AS users FROM radacct AS Radacct where realm='".$realm."' AND acctstarttime >='".$start_date."' AND acctstoptime <='".$end_date."'";
        $qr = $this->Radacct->query($query);

        $ra             = array();
        $ra['tx'] = $total_input;
        $ra['rx']= $total_output;
        $ra['total']  = $total_input + $total_output;
        $ra['users']       = $qr[0][0]['users'];

        return $ra;

    }


}
?>