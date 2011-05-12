<?
class AccessPointsController extends AppController {
    var $name       = 'AccessPoints';
    var $uses       = array('Na','NaRealm','Realm','RogueAp','WirelessClient');   //Tables to check for recent changes
    var $components = array('Session','Dojolayout','Rights','Json','CmpNas','Formatter');    //Add the locker component
    var $helpers    = array('Javascript');


    function wip(){
        $this->layout = 'ajax';
        $json_return = array();

        $this->set('json_return',$json_return);
    }

    function json_index($quick=false){  //For the dojo Grid
       
        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        //---------------------------------------------------------------------
        $auth_data = $this->Session->read('AuthInfo');
        $r = $this->Na->find('all',array('conditions' => array('Na.nasname <>' => '127.0.0.1','Na.type' => 'Open-Wrt[Ent]'))); //Get all the NAS devices EXCEPT 127.0.0.1
        //print_r($r);

        //Loop through it and check the user's rights decide whether to display or not
        foreach($r as $entry){

            $na_id      = $entry['Na']['id'];
            $last_state = 'No Data';

            if($entry['Na']['monitor'] == 1){
                //Get the last state
                if(count($entry['NaState']) > 0){
                    $last_state = $entry['NaState'][0]['state'];
                    if($last_state == '1'){
                        $last_state = 'Up';
                    }
                    if($last_state == '0'){
                        $last_state = 'Down';
                    }
                    $state_time = $this->Formatter->diff_in_time($entry['NaState'][0]['created']);
                    $last_state = $last_state." ( $state_time )";
                }
            }

            //---------------------------------------------------------------------------------------------------------
            //this will return an array containing a 'show' and an 'available_to' key - if show == false will not show
            $return_array       = $this->CmpNas->getRealmsForNa($na_id);
            //---------------------------------------------------------------------------------------------------------

            if($return_array['show']== true){       
                
                $connected  = count($entry['WirelessClient']);
                $rogue      = 0;
                $rogues     = $entry['RogueAp'];

                foreach($rogues as $r){
                    if($r['state'] == 'Unknown'){
                        $rogue ++;
                    }
                }

                //$quick only updates 'status' and 'connected'
                if($quick){
                    array_push($json_return['items'],array(
                                                        'id'            =>  $entry['Na']['id'],
                                                        'status'        =>  $last_state,
                                                        'clients'       =>  $connected
                    ));
                }else{
                     array_push($json_return['items'],array(
                                                        'id'            =>  $entry['Na']['id'],
                                                        'nasname'       =>  $entry['Na']['nasname'],
                                                        'shortname'     =>  $entry['Na']['shortname'],
                                                        'type'          =>  $entry['Na']['type'],
                                                        'status'        =>  $last_state,
                                                        'clients'       =>  $connected,
                                                        'rogue'         =>  $rogue
                    ));
                }
            }
        }
        //----------------------------------------------------------

        //----------------------------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_device($nas_id){
        $this->layout = 'ajax';
        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        //-----------------------------------------

        $qr     = $this->Na->find('first',array('conditions' => array('Na.id' => $nas_id)));
        $ip     = $qr['Na']['nasname'];

        $json_return['device'] = $this->_device_info($ip);

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }


    function json_rogues_index($nas_id,$refresh=false){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        if($refresh){
            $this->single_rogue_check($nas_id);
        }

        $qr = $this->RogueAp->findAllByNaId($nas_id);
        foreach($qr as $item){
            $id     = $item['RogueAp']['id'];
            $ssid   = $item['RogueAp']['ssid'];
            $mac    = $item['RogueAp']['mac'];
            $mode   = $item['RogueAp']['mode'];
            $channel= $item['RogueAp']['channel'];
            $enc= $item['RogueAp']['encryption'];
            $state  = $item['RogueAp']['state'];
            $modified= $item['RogueAp']['modified'];

            array_push($json_return['items'],array('id' => $id,'ssid' => $ssid,'mac' => $mac, 'mode' => $mode,'channel' => $channel, 'encryption'=> $enc,'state' => $state, 'modified' => $modified));
        }


        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_clients_index($nas_id){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------

        $this->single_client_check($nas_id);//Do the latest client fetch from device
        //$qr = $this->WirelessClient->findAllByNaId($nas_id);
        $qr = $this->WirelessClient->find('all',array('conditions' => array('WirelessClient.na_id' => $nas_id,'WirelessClient.active' => 'yes')));
        
        foreach($qr as $item){

            $id     = $item['WirelessClient']['id'];
            $ssid   = $item['WirelessClient']['ssid'];
            $mac    = $item['WirelessClient']['mac'];
            $chan   = $item['WirelessClient']['chan'];
            $rate   = $item['WirelessClient']['rate'];
            $rssi   = $item['WirelessClient']['rssi'];
            $mod    = $item['WirelessClient']['modified'];
            array_push($json_return['items'],array('id' => $id,'ssid' => $ssid,'mac' => $mac, 'channel' => $chan,'rate' => $rate,'rssi' => $rssi, 'modified' => $mod));

        }


        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }


    function json_change_state(){

        $this->layout = 'ajax';
        $state   = $this->params['form']['state'];
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                //----------------
                $rogue_ap_id = $this->params['url'][$key];
                $this->RogueAp->id = $rogue_ap_id;
                $this->RogueAp->saveField('state', $state);
                //-------------
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
                $rogue_ap_id = $this->params['url'][$key];
                $this->RogueAp->del($rogue_ap_id,true);
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


    function json_power(){

         $this->layout = 'ajax';
        $json_return = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $id     = $this->params['url'][$key];
                $r      = $this->Na->find('first',array('conditions' => array('Na.nasname <>' => '127.0.0.1','Na.type' => 'Open-Wrt[Ent]','Na.id' => $id)));
                $ip     = $r['Na']['nasname'];
                $last_line = system('ssh -o ConnectTimeout=2 root@'.$ip.' /sbin/reboot', $retval);
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }


    function client_check(){
        //we need to run through all tha nas devices 
        $qr = $this->Na->find('all',array('conditions' => array('Na.type' => 'Open-Wrt[Ent]')));
        foreach($qr as $item){
            $ip     = $item['Na']['nasname'];
            $nas_id = $item['Na']['id'];
            print ("Find Wireless Clients for $ip\n");
           # $this->WirelessClient->deleteAll(array('WirelessClient.na_id' => $nas_id),true);
            $this->_update_wireless_clients($nas_id,$ip);
        }
    }

    function single_client_check($nas_id){

        $qr     = $this->Na->find('first',array('conditions' => array('Na.type' => 'Open-Wrt[Ent]','Na.id' => $nas_id)));
        $ip     = $qr['Na']['nasname'];
        $nas_id = $qr['Na']['id'];
    #    $this->WirelessClient->deleteAll(array('WirelessClient.na_id' => $nas_id),true);
        $this->_update_wireless_clients($nas_id,$ip);
    }


    function rogue_check(){

        //we need to run through all tha nas devices 
        $qr = $this->Na->find('all',array('conditions' => array('Na.type' => 'Open-Wrt[Ent]')));
        foreach($qr as $item){
            $ip     = $item['Na']['nasname'];
            $nas_id = $item['Na']['id'];
            print ("Find Rogues for $ip\n");
            $rogues = $this->_find_rogues($ip);
            $this->_update_rogues($nas_id,$rogues);
        }
    }

    function single_rogue_check($nas_id){

        $qr = $this->Na->findById($nas_id);
        $ip = $qr['Na']['nasname'];
        $rogues = $this->_find_rogues($ip);
        $this->_update_rogues($nas_id,$rogues);
    }

    function _update_wireless_clients($na_id,$ip){

        $clients        = array();
        $interfaces     = array('ath0','ath1');
        //active => 'no' for all macs that were ever attached to this $na_id
        $this->WirelessClient->updateAll(array('WirelessClient.active'=> "'no'"),array('WirelessClient.na_id'=> $na_id));

        foreach($interfaces as $i){

            $output     = array();
            $clients    = array();

            exec('ssh  -o ConnectTimeout=2 root@'.$ip." /usr/sbin/wlanconfig $i list sta",$clients);
            if(count($clients) > 0){
                $count=0;
                foreach($clients as $client){
                    if($count != 0){

                        if(preg_match('/QoSInfo/',$client)){
				continue;
			}
                        ($i == 'ath0')&&($ssid = 'Ent Wireless');
                        ($i == 'ath1')&&($ssid = 'Ent Guest');
                        $parts  = preg_split("/\s+/",$client);
                        $d      = array();
                        $d['WirelessClient']['na_id']   = $na_id;
                        $d['WirelessClient']['ssid']    = $ssid;
                        $d['WirelessClient']['mac']     = $parts[0];
                        $d['WirelessClient']['aid']     = $parts[1];
                        $d['WirelessClient']['chan']    = $parts[2];
                        $d['WirelessClient']['rate']    = $parts[3];
                        $d['WirelessClient']['rssi']    = $parts[4];
                        $d['WirelessClient']['active']  = 'yes';

                        //Check is it has not already been listed
                        $check_if_there = $this->WirelessClient->find('first',array('conditions' => array('WirelessClient.mac' => $parts[0])));
                        if($check_if_there != ''){
                            $this->WirelessClient->id = $check_if_there['WirelessClient']['id'];
                        }

                        $this->WirelessClient->save($d);
                        $this->WirelessClient->id       = null;
                    }
                    $count++;
                }
            }
        }
    }

    function _device_info($ip){

        $return_array   = array();
        $return_array['time']   = 'NA';
        $return_array['up']     = 'NA';
        $return_array['load']   = 'NA';
        $return_array['fw']     = 'NA';


        $output         = array();
        exec('ssh  -o ConnectTimeout=2 root@'.$ip.' uptime',$output);
        //exec('uptime',$output);
        $uptime = $output[0];
        if(preg_match("/\s+up\s+/",$uptime)){
            $time           = preg_replace("/\s+up\s+.*/",'', $uptime);
            $up             = preg_replace("/.*up\s+/",'', $uptime);
            $up             = preg_replace("/,.*/",'', $up);
            $load           = preg_replace("/.*load average:\s+/",'', $uptime);
        }

        
        $output         = array();
        exec('ssh  -o ConnectTimeout=2 root@'.$ip.' uname -a',$output);
        //exec('uname -a',$output);
        $fw = $output[0];

        //---Do a ping test for tunnel gateway----------------
	$nr_ok 		= 0;
        $output		= array();
        exec('ssh  -o ConnectTimeout=2 root@'.$ip.' ping 10.8.0.1 -c 2',$output);
        foreach($output as $i){
            if(preg_match("/received/",$i)){
                $nr_ok = preg_replace("/^.+\s+packets\s+transmitted,\s+/",'', $i); //Remove the start
                $nr_ok = preg_replace("/\s+received.+/",'', $i);
            }
        }

        if($nr_ok >= 1){
            $tunnel = 'Up';
        }else{
            $tunnel = 'Down';
        }
        //------------------------------------------------------

        $return_array['time']   = $time;
        $return_array['up']     = $up;
        $return_array['load']   = $load;
        $return_array['fw']     = $fw;
        $return_array['tunnel'] = $tunnel;

        return $return_array;
    }


    function _update_rogues($nas_id,$rogues){

        foreach($rogues as $item){

            $already_there = $this->RogueAp->find('first',
                                array('conditions' => 
                                    array(  'RogueAp.na_id' => $nas_id,
                                            'RogueAp.mac' => $item['mac']
                                    )));
            $id = '';
            if($already_there != ''){
                $id     = $already_there['RogueAp']['id'];
            }

            $data   = array();
            $data['RogueAp']['id']      = $id;
            $data['RogueAp']['na_id']   = $nas_id;
            $data['RogueAp']['ssid']    = $item['ssid'];
            $data['RogueAp']['mac']     = $item['mac'];
            $data['RogueAp']['mode']    = $item['mode'];
            $data['RogueAp']['channel'] = $item['channel'];
            $data['RogueAp']['quality'] = $item['quality'];
            $data['RogueAp']['signal']   = $item['signal'];
            $data['RogueAp']['noise']   = $item['noise'];
            $data['RogueAp']['encryption']= $item['encryption'];
            $this->RogueAp->save($data);
        }
    }


    function _find_rogues($ip){

        $rogues         = array();
        $output         = array();
        exec('ssh  -o ConnectTimeout=2 root@'.$ip.' /usr/sbin/iwlist ath0 scan',$output);

        $counter        = -1;
        $enc_sub_flag   = false;
        foreach($output as $line){

            if(preg_match("/^\s+Cell\s+/",$line)){
                $counter++;
                $mac            = preg_replace("/.*Address:\s*/",'', $line);
                $rogues[$counter]['mac'] = $mac;
            }

            if(preg_match("/^\s+ESSID:\s*/",$line)){
                $ssid           = preg_replace("/.*ESSID:\s*/",'', $line);
                $ssid           = preg_replace('/"/','', $ssid);
                $rogues[$counter]['ssid'] = $ssid;
            }

            if(preg_match("/^\s+Mode:\s*/",$line)){
                $mode           = preg_replace("/.*Mode:\s*/",'', $line);
                $rogues[$counter]['mode'] = $mode;
            }

            if(preg_match("/^\s+Frequency:\s*.*Channel/",$line)){
                $channel           = preg_replace("/.*Frequency:\s*.*\Channel/",'', $line);
                $channel           = preg_replace("/\)/",'', $channel);
                $rogues[$counter]['channel'] = $channel;
            }

            if(preg_match("/^\s+Quality=/",$line)){
                $quality           = preg_replace("/^\s+Quality=/",'', $line);
                $quality           = preg_replace("/\s+Signal.*/",'', $quality);

                $signal            = preg_replace("/.*\s+Signal\s+level=/",'', $line);
                $signal            = preg_replace("/\s+dBm.*/",'', $signal);

                $noise             = preg_replace("/.*\s+Noise\s+level=/",'', $line);
                $noise             = preg_replace("/\s+dBm.*/",'', $noise);

                $rogues[$counter]['quality'] = $quality;
                $rogues[$counter]['signal']  = $signal;
                $rogues[$counter]['noise']   = $noise;
            } 

            if(preg_match("/^\s+Encryption\s+key:/",$line)){
                $encryption                       = preg_replace("/^\s+Encryption\s+key:/",'', $line);
                //if $encryption = on, default to wep then we may change it depending if other values are present
                ($encryption == 'on')&&($encryption = 'WEP');
                $rogues[$counter]['encryption']   = $encryption;
            }

            //-----EnGenius hack ---------------------------------------------------
            //--- They do not report on the type of encryption but rather report----
            //---  (Unknown Wireless Token 0x8C05) [times two for WPA2 Personal]----
            //---  (Unknown Wireless Token 0x8C05) [times one for WPA2 Enterprise]--
             if(preg_match("/Unknown\s+Wireless\s+Token\s+0x8C05/",$line)){
                if($rogues[$counter]['encryption'] == 'WPA2 Enterprise'){ //Second time - make it Personal as it has that X2
                    $rogues[$counter]['encryption']   = 'WPA2 Personal';
                }else{
                    $rogues[$counter]['encryption']   = 'WPA2 Enterprise';  //First time - make it Enterprise as it has that X1
                }
            }
            //----------------------------------------------------------------------

            //Test for WPA 
            if(preg_match("/^\s+IE:\s+IEEE\s+802\.11i\/WPA2/",$line)){
                $enc_sub_flag = true;
            }

            if($enc_sub_flag &&(preg_match("/^\s+Authentication\s+Suites/",$line))){
                if(preg_match("/802\.1x/",$line)){
                    $rogues[$counter]['encryption']   = 'WPA2 Enterprise';
                }
                if(preg_match("/PSK/",$line)){
                    $rogues[$counter]['encryption']   = 'WPA2 Personal';
                }
                $enc_sub_flag = false; //reset the flag
            }
        }
        return $rogues;
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
        $json_return['items']             = $this->Dojolayout->actions_for_access_points();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
        //print_r($json_return);
    }

    function json_actions_rogues(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on templates tab----------------- 
        //--AP Sepcific: YES --------------------------------------------------------
        //--Rights Completed:   NA -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                     = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_rogues();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
        //print_r($json_return);
    }

    function json_actions_clients(){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on templates tab----------------- 
        //--AP Sepcific: YES --------------------------------------------------------
        //--Rights Completed:   NA -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                     = 'ajax';
        $json_return['items']             = $this->Dojolayout->actions_for_clients();
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);

    }

}
?>
