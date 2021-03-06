<?
class RealmsController extends AppController {
    var $name       = 'Realms';
    var $helpers    = array('Javascript');
    var $uses       = array('Realm','UserRealm','Check','Radacct','Photo');

    var $components = array('Session','Rights','Json','Freeradius','Formatter');    //Add the locker component

    //var $scaffold;

     function beforeFilter() {
       $this->Auth->allow('json_new_photo','json_photos_for_realm');
    }

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
        $this->data['Realm']['street_no']       = $this->params['form']['StreetNo'];
        $this->data['Realm']['street']          = $this->params['form']['Street'];
        $this->data['Realm']['town_suburb']     = $this->params['form']['TownSuburb'];
        $this->data['Realm']['city']            = $this->params['form']['City'];

        

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
        $d['Realm']['street_no']            = $this->params['form']['StreetNo'];
        $d['Realm']['street']               = $this->params['form']['Street'];
        $d['Realm']['town_suburb']          = $this->params['form']['TownSuburb'];
        $d['Realm']['city']                 = $this->params['form']['City'];
        $d['Realm']['lon']                  = $this->params['form']['Lon'];
        $d['Realm']['lat']                  = $this->params['form']['Lat'];

        //If the append_string_to_user have changed, we remove the old one and replace it
        $q_r = $this->Realm->findById($this->params['form']['id']);
        if($q_r){

            $old_name   = $q_r['Realm']['append_string_to_user'];
            $new_name   = $this->params['form']['Append'];
            $this->Freeradius->realm_del($old_name);
            $this->Freeradius->realm_add($new_name);
        }
        $this->Realm->save($d);

        //Find the entry and see if the lon and lat has values which we can return
        $q_r = $this->Realm->findById($this->params['form']['id']);

        if($q_r){
            $lon = $q_r['Realm']['lon'];
            $lat = $q_r['Realm']['lat'];
            $json_return['json']['location']['lon']  = $lon;
            $json_return['json']['location']['lat']  = $lat;
        }

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

        /* We are doing away with scaling the uploaded graphic to a smaller size but rather resize a master copy dynamically
        exec("/usr/bin/mogrify -resize x50 $file_temp");
        exec("cp $file_temp $new_file");
        exec("chmod 644 $new_file");
        */
        
        rename("$file_temp", "$new_file");
        chmod("$new_file", 0644);   

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


    function json_new_photo($realm_id){


        $this->layout = 'ajax';

        //This is the uploaded file's detail
        $filename   = 'fileToUpload'.$realm_id;
        $file_temp  = $_FILES[$filename]['tmp_name'];
        $name       = $_FILES[$filename]['name'];
        $extension  = $_FILES[$filename]['type'];

        $extension  = preg_replace('/.+\//','',$extension);
        Configure::load('yfi');
        $directory  = Configure::read('realm.icon_directory');

        //This is the realm id, title and the description (if description is supplied)
        $realm_id   = $this->params['form']['id'];
        $title      = $this->params['form']['title'];


        $description = '';
        if(array_key_exists('description',$this->params['form'])){
            $description = $this->params['form']['description'];
        }

        $this->data = array();
        $this->data['Photo']['id']              = "";
        $this->data['Photo']['realm_id']        = $realm_id;
        $this->data['Photo']['title']           = $title;
        $this->data['Photo']['description']     = $description;
        $this->data['Photo']['file_name']       = '';
       
        $this->Photo->save($this->data);

        //Get the id for our filename:
        $new_photo_id = $this->Photo->id;

        //Update the filename
        $this->Photo->saveField('file_name',$new_photo_id.'.'."$extension");
        

        rename("$file_temp", "$directory/$new_photo_id".'.'."$extension");
        chmod("$directory/$new_photo_id".'.'."$extension", 0644);   


        $json_return['json']['status']      = 'ok';
      /*  
        $json_return['json']['file']        = $file_temp;
        $json_return['json']['file_name']   = $name;
        $json_return['json']['file_ext']    = $extension;
        $json_return['json']['realm_id']    = $realm_id;
        $json_return['json']['title']       = $title;
        $json_return['json']['description'] = $description;
        */

        //Filter out the /var/www
        $directory = preg_replace("/\/var\/www/","", $directory);
        $directory = preg_replace("/\/usr\/share\/nginx\/www/","", $directory);

        $json_return['photo']['id']         = $this->Photo->id;
        $json_return['photo']['title']      = $title;
        $json_return['photo']['description']= $description;
        $json_return['photo']['picture' ]   = "$directory/$new_photo_id".'.'."$extension";   
        $this->set('json_return',$json_return);

    }


      function json_edit_photo($photo_id){


        $this->layout = 'ajax';

        //This is the uploaded file's detail
        $filename   = 'fileToUpload'.$photo_id;
        $file_temp  = $_FILES[$filename]['tmp_name'];
        $name       = $_FILES[$filename]['name'];
        $extension  = $_FILES[$filename]['type'];

        $extension  = preg_replace('/.+\//','',$extension);
        Configure::load('yfi');
        $directory  = Configure::read('realm.icon_directory');

        //Replace existing photo
        rename("$file_temp", "$directory/$photo_id".'.'."$extension");
        chmod("$directory/$photo_id".'.'."$extension", 0644);   // decimal; probably incorrect


        //This is the realm id, title and the description (if description is supplied)
        $realm_id   = $this->params['form']['id'];
        $title      = $this->params['form']['title'];

        $this->Photo->id = $photo_id;
        $this->Photo->saveField('file_name',$photo_id.'.'."$extension");    //The extension may have changed
        $this->Photo->saveField('title',$title);    //The extension may have changed


        $description = '';
        if(array_key_exists('description',$this->params['form'])){
            $description = $this->params['form']['description'];
            $this->Photo->saveField('description',$description);    //The extension may have changed
        }

        $json_return['json']['status']      = 'ok';

        //Filter out the /var/www
        $directory = preg_replace("/\/var\/www/","", $directory);
        $directory = preg_replace("/\/usr\/share\/nginx\/www/","", $directory);

        $json_return['photo']['id']         = $photo_id;
        $json_return['photo']['title']      = $title;
        $json_return['photo']['description']= $description;
        $json_return['photo']['picture' ]   = "$directory$photo_id".'.'."$extension";   
        $this->set('json_return',$json_return);

    }


    function json_photos_for_realm($realm_id){

        $this->layout = 'ajax';
        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok';
        $json_return['label']             = 'title';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //-- Query the DB ---------------------------------------------
        $r = $this->Photo->find(
                            'all',
                            array(
                                'conditions'=> array('Photo.realm_id' =>$realm_id),
                                'order' => 'Photo.created ASC',
                            )
            );
        Configure::load('yfi');
        $directory  = Configure::read('realm.icon_directory');
        //Filter out the /var/www
        $directory = preg_replace("/\/var\/www/","", $directory);
        $directory = preg_replace("/\/usr\/share\/nginx\/www/","", $directory);

        $count = 0;
        foreach($r as $entry){
            if($count == 0){
                array_push(
                    $json_return['items'],
                        array(
                            'title'         => $entry['Photo']['title'],
                            'description'   => $entry['Photo']['description'], 
                            'id'            => $entry['Photo']['id'], 
                            'realm_id'      => $entry['Photo']['realm_id'],
                            'picture'       => $directory.$entry['Photo']['file_name'],  
                            'selected'      => 'selected')); //Select the first one
            }else{
                array_push($json_return['items'],
                    array(
                            'title'         => $entry['Photo']['title'],
                            'description'   => $entry['Photo']['description'], 
                            'id'            => $entry['Photo']['id'],
                            'realm_id'      => $entry['Photo']['realm_id'],
                            'picture'       => $directory.$entry['Photo']['file_name'], 
                            'selected'      => ''));
            }
            $count ++;
        }
        //---------------------------------------------------------
        $this->set('json_return',$json_return);
    }


    function json_delete_photo_for_realm($photo_id){
        $this->layout = 'ajax';

        //We need to determine where the file is and remove the file before removing it from the DB.
        $q_r = $this->Photo->findById($photo_id);
        if($q_r){
            Configure::load('yfi');
            $directory  = Configure::read('realm.icon_directory');
            $filename = $q_r['Photo']['file_name'];
            unlink($directory.$filename);
        }
        //Delete photo
        $this->Photo->del($photo_id,false);
        $json_return = array();
        $json_return['json']['status']    = 'ok';
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
