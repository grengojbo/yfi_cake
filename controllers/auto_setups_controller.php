<?
class AutoSetupsController extends AppController {
    var $name       = 'AutoSetups';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component
    var $uses       = array('AutoSetup','AutoMac','AutoGroup');

    var $scaffold;

    function beforeFilter() {

       $this->Auth->allow('configuration_for');
    }


    function json_index(){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'mac';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();
        //-----------------------------------------


        $qr = $this->AutoMac->find('all',array());
       // print_r($qr);
        foreach($qr as $item){
            $id     = $item['AutoMac']['id'];
            $mac    = $item['AutoMac']['name'];

            $ip         = '';
            $tun_ip     = '';
            $contact_ip = '';
            if($item['AutoMac']['contact_ip'] == ''){
                $contact= 'never';
            }else{
                $contact    = $item['AutoMac']['modified'];
                $contact_ip = $item['AutoMac']['contact_ip'];
            }

            foreach($item['AutoSetup'] as $setting){
                ($setting['description'] == 'ip')&&($ip=$setting['value']);
                ($setting['description'] == 'tun_ip')&&($tun_ip=$setting['value']);
            }

            array_push($json_return['items'],array('id'=>$id,'mac'=> $mac,'ip' => $ip,'vpn_ip' => $tun_ip, 'contact_ip' => $contact_ip,'last_contact' => $contact,));

        }

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }



    function json_add(){

        $this->layout = 'ajax';


        $mac    = $this->params['form']['mac'];

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok';
        //-----------------------------------------


        //See if the device is already present
        if($this->AutoMac->find('count',array('conditions'=> array('AutoMac.name' => $mac))) > 0){

            $json_return['json']['status']    = 'error';
            $json_return['json']['detail']    = gettext('MAC Address already defined');

        }else{


            $d                      = array();
            $d['AutoMac']['name']   = $mac;
            $this->AutoMac->save($d);
            $mac_id                 = $this->AutoMac->id;

            //Get the ID of Network group
            $q_r                    = $this->AutoGroup->find('first',array('conditions'=>array('Name' => 'Network')));

            $values                 = array();
            $values['ip']           = $this->params['form']['ip'];
            $values['mask']         = $this->params['form']['mask'];
            $values['gateway']      = $this->params['form']['gateway'];
            $values['dns']          = $this->params['form']['dns'];

            foreach(array_keys($values)as $key){

                $value                          = $values[$key];
                $d                              = array();
                $d['AutoSetup']['description']  = $key;
                $d['AutoSetup']['value']        = $value;
                $d['AutoSetup']['auto_group_id']= $q_r['AutoGroup']['id'];
                $d['AutoSetup']['auto_mac_id']  = $mac_id;

                $this->AutoSetup->save($d);
                $this->AutoSetup->id = false;
            } 
        }

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }

    function json_del(){

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
                $auto_mac_id = $this->params['url'][$key];
                $this->AutoMac->del($auto_mac_id,true);
                //Seems it does not cascade delete
                $this->AutoSetup->deleteAll(array('AutoSetup.auto_mac_id' => $auto_mac_id),true);
            }
        }
        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }



    function json_network_view($id){

        $this->layout = 'ajax';

        $json_return= array();
        $json_return['json']['status']  = "ok";
        $json_return['network']         = array();


        $qr = $this->AutoMac->find('first',array('conditions' =>array('AutoMac.id' => $id)));

        $json_return['network']['mac']  = $qr['AutoMac']['name'];
        $json_return['network']['ip']     = '';
        $json_return['network']['gateway']= '';
        $json_return['network']['mask']   = '';
        $json_return['network']['dns']    = '';

        foreach($qr['AutoSetup'] as $setting){
            ($setting['description'] == 'ip')&&($json_return['network']['ip']=$setting['value']);
            ($setting['description'] == 'gateway')&&($json_return['network']['gateway']=$setting['value']);
            ($setting['description'] == 'mask')&&($json_return['network']['mask']=$setting['value']);
            ($setting['description'] == 'dns')&&($json_return['network']['dns']=$setting['value']);
        }
        $this->set('json_return',$json_return);
    }

    function json_network_edit(){


        $this->layout = 'ajax';

        //Get the ID of Network group
        $q_r        = $this->AutoGroup->find('first',array('conditions'=>array('Name' => 'Network')));

        $id         = $this->params['form']['id'];
        $mac        = $this->params['form']['mac'];
        $ip         = $this->params['form']['ip'];

        //Do the IP
        $this->_set_auto_setup_value($id,'ip',$ip,$q_r['AutoGroup']['id']);

        //Do the gateway
        $this->_set_auto_setup_value($id,'gateway',$this->params['form']['gateway'],$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'mask',$this->params['form']['mask'],$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'dns',$this->params['form']['dns'],$q_r['AutoGroup']['id']);

        //-----------------------------------------------------------------------------------------
        //Do the MAC -if it changed we need to 'trigger' a change for all so that all the configs can get down to the device
        $q_mac      = $this->AutoMac->find('first',array('conditions'=> array('id' => $id)));
        if($q_mac   != ''){

            $old_mac = $q_mac['AutoMac']['name'];
            if($old_mac != $mac){
                $d          = array();
                $d['AutoMac']['id']     = $id;
                $d['AutoMac']['name']   = $mac;
                $this->AutoMac->save($d);

                //____Trigger the VPN___
                $q_vpn = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'vpn_server','AutoSetup.auto_mac_id' => $id)));
                if($q_vpn != ''){
                    $d_vpn['AutoSetup']['id']    = $q_vpn['AutoSetup']['id'];
                    $this->AutoSetup->save($d_vpn);
                }

                //____Trigger the wireless____
            }
        }
        //-----------------------------------------------------------------------------------------


        $json_return= array();
        $json_return['json']['status']  = "ok";
        $this->set('json_return',$json_return);

    }

    function json_vpn_view($id){

        $this->layout = 'ajax';

        $json_return= array();
        $json_return['json']['status']  = "ok";
        $json_return['network']         = array();


        $qr = $this->AutoMac->find('first',array('conditions' =>array('AutoMac.id' => $id)));

        $json_return['vpn']['vpn_server']   = '';
        $json_return['vpn']['tun_ip']       = '';
        $json_return['vpn']['tun_mask']     = '';
        $json_return['vpn']['tun_broadcast']= '';
        $json_return['vpn']['ca']           = '';
        $json_return['vpn']['cert']         = '';
        $json_return['vpn']['key']          = '';

        foreach($qr['AutoSetup'] as $setting){
            ($setting['description'] == 'vpn_server')&&($json_return['vpn']['vpn_server']   = $setting['value']);
            ($setting['description'] == 'tun_ip')&&($json_return['vpn']['tun_ip']           = $setting['value']);
            ($setting['description'] == 'tun_mask')&&($json_return['vpn']['tun_mask']       = $setting['value']);
            ($setting['description'] == 'tun_broadcast')&&($json_return['vpn']['tun_broadcast']=$setting['value']);
            ($setting['description'] == 'ca')&&($json_return['vpn']['ca']                   = $setting['value']);
            ($setting['description'] == 'cert')&&($json_return['vpn']['cert']               = $setting['value']);
            ($setting['description'] == 'key')&&($json_return['vpn']['key']                 = $setting['value']);
        }
        $this->set('json_return',$json_return);
    }


     function json_vpn_edit(){

        $this->layout = 'ajax';
        //Get the ID of Network group
        $q_r        = $this->AutoGroup->find('first',array('conditions'=>array('Name' => 'OpenVPN')));

        $id             = $this->params['form']['id'];
        $vpn_server     = $this->params['form']['vpn_server'];

        $tun_ip         = $this->params['form']['tun_ip'];
        $tun_mask       = $this->params['form']['tun_mask'];
        $tun_broadcast  = $this->params['form']['tun_broadcast'];

        $ca             = $this->params['form']['ca'];
        $cert           = $this->params['form']['cert'];
        $key            = $this->params['form']['key'];

        //Do the VPN Server
        $this->_set_auto_setup_value($id,'vpn_server',$vpn_server,$q_r['AutoGroup']['id']);

        //Do the tunnel specifics
        $this->_set_auto_setup_value($id,'tun_ip',          $tun_ip,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'tun_mask',        $tun_mask,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'tun_broadcast',   $tun_broadcast,$q_r['AutoGroup']['id']);

        //Do the certificate, key and ca
        $this->_set_auto_setup_value($id,'ca',              $ca,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'cert',            $cert,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'key',             $key,$q_r['AutoGroup']['id']);

        $json_return= array();
        $json_return['json']['status']  = "ok";
        $this->set('json_return',$json_return);

    }


    function json_wireless_view($id){

        $this->layout = 'ajax';

        $json_return= array();
        $json_return['json']['status']  = "ok";
        $json_return['network']         = array();

        $qr = $this->AutoMac->find('first',array('conditions' =>array('AutoMac.id' => $id)));

        $json_return['wireless']['enabled']         = true;
        $json_return['wireless']['channel']         = '5';
        $json_return['wireless']['power']           = '100';
        $json_return['wireless']['distance']        = '60';
        $json_return['wireless']['secure_ssid']     = '';
        $json_return['wireless']['radius']          = '';
        $json_return['wireless']['secret']          = '';
        $json_return['wireless']['open_ssid']       = '';

        foreach($qr['AutoSetup'] as $setting){
            ($setting['description'] == 'enabled')&&($json_return['wireless']['enabled']    = $setting['value']);
            ($setting['description'] == 'channel')&&($json_return['wireless']['channel']    = $setting['value']);
            ($setting['description'] == 'power')&&($json_return['wireless']['power']        = $setting['value']);
            ($setting['description'] == 'distance')&&($json_return['wireless']['distance']  =$setting['value']);
            ($setting['description'] == 'secure_ssid')&&($json_return['wireless']['secure_ssid']= $setting['value']);
            ($setting['description'] == 'radius')&&($json_return['wireless']['radius']      = $setting['value']);
            ($setting['description'] == 'secret')&&($json_return['wireless']['secret']      = $setting['value']);
            ($setting['description'] == 'open_ssid')&&($json_return['wireless']['open_ssid']= $setting['value']);
        }
        $this->set('json_return',$json_return);
    }

    function json_wireless_edit(){

        $this->layout = 'ajax';
        //Get the ID of Network group
        $q_r        = $this->AutoGroup->find('first',array('conditions'=>array('Name' => 'Wireless')));

        $id             = $this->params['form']['id'];

        $enabled        = false;
        if(array_key_exists('enabled',$this->params['form'])){
            $enabled    = true;
        }

        $channel        = $this->params['form']['channel'];
        $power          = $this->params['form']['power'];
        $distance       = $this->params['form']['distance'];

        $secure_ssid    = $this->params['form']['secure_ssid'];
        $radius         = $this->params['form']['radius'];
        $secret         = $this->params['form']['secret'];

        $open_ssid      = $this->params['form']['open_ssid'];

        $this->_set_auto_setup_value($id,'enabled', $enabled,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'channel', $channel,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'power',   $power,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'distance',$distance,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'secure_ssid',$secure_ssid,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'radius',  $radius,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'secret',  $secret,$q_r['AutoGroup']['id']);
        $this->_set_auto_setup_value($id,'open_ssid',$open_ssid,$q_r['AutoGroup']['id']);

        $json_return= array();
        $json_return['json']['status']  = "ok";
        $this->set('json_return',$json_return);

    }

    function configuration_for($mac){

        $this->layout = 'ajax';

        //Mac will arrive in form XX-XX-XX-XX-XX-XX we must get it in form XX:XX:XX:XX:XX:XX
        $mac = preg_replace('/-/', ':', $mac);

        
        //Update the AutoMac table to show this request
        $qr = $this->AutoMac->find('first',array('conditions' => array('AutoMac.name' => $mac)));
        if($qr == ''){
            $this->set('config_string','');
            return;
        }

         
        if($qr != ''){
            $request_from = $_SERVER["REMOTE_ADDR"];
            $d['AutoMac']['id']         = $qr['AutoMac']['id'];
            $d['AutoMac']['contact_ip'] = $request_from;
            $this->AutoMac->save($d);

            $modified                   = $qr['AutoMac']['modified'];
            $mac_id                     = $qr['AutoMac']['id'];
        }

        
        $fb =   "file_name:\n".
                "/etc/config/network\n".
                "file_content:\n".
                $this->_return_network_settings($mac);

        //Get the VPN detail - if required
        $vpn_string = $this->_return_vpn($mac_id,$modified);
        $fb = $fb.$vpn_string;

        //Get the Wireless detail - if required
        $wireless_string = $this->_return_wireless($mac_id,$modified);
        $fb = $fb.$wireless_string;

        $this->set('config_string',$fb);

    }


    //Add CSV exporting



    function _set_auto_setup_value($mac_id,$descr,$val,$group_id){

        $qr = $this->AutoSetup->find('first',array('conditions'=> array('AutoSetup.auto_mac_id' => $mac_id,'description' => $descr,'auto_group_id' => $group_id)));
        //print_r($qr);
        $id = '';
        if($qr != ''){
            $id = $qr['AutoSetup']['id'];
        }


        $d['AutoSetup']['id']           = $id;
        $d['AutoSetup']['auto_mac_id']  = $mac_id;
        $d['AutoSetup']['description']  = $descr;
        $d['AutoSetup']['value']        = $val;
        $d['AutoSetup']['auto_group_id']= $group_id;
        $this->AutoSetup->save($d);
    }


    function _return_network_settings($mac){

        $qr = $this->AutoMac->find('first',array('conditions' => array('AutoMac.name' => $mac)));
        
        $ip         = '';
        $gateway    = '';
        $mask       = '';
        $dns        = '';

        foreach($qr['AutoSetup'] as $setting){
            ($setting['description'] == 'ip')&&($ip = $setting['value']);
            ($setting['description'] == 'gateway')&&($gateway = $setting['value']);
            ($setting['description'] == 'mask')&&($mask = $setting['value']);
            ($setting['description'] == 'dns')&&($dns = $setting['value']);
        }

        $network = "\nconfig 'interface' 'loopback'\n".
                    "option 'ifname' 'lo'\n".
                    "option 'proto' 'static'\n".
                    "option 'ipaddr' '127.0.0.1'\n".
                    "option 'netmask' '255.0.0.0'\n".
                    "\n".
                    "config 'interface' 'lan'\n".
                    "option 'ifname' 'eth0'\n".
                    "option 'type' 'bridge'\n".
                    "option 'proto' 'static'\n".
                    "option 'netmask' '$mask'\n".
                    "option 'ipaddr' '$ip'\n".
                    "option 'gateway' '$gateway'\n".
                    "option 'dns' '$dns'\n\n";
        return $network;
    }


    function _return_vpn($mac_id,$modified){

        $return_string = '';

        //_________ Start off with the /etc/openvpn/my-vpn.conf file ____________
        //__ Settings to check on this one:______________________________________
        //__ 1.) vpn_server _____________________________________________________

        //---Check if there was changes since the last contact on this key----
        $fb_value = $this->_changes_since_last_contact_for($mac_id,'vpn_server',$modified);

        if(! $fb_value){
            return $return_string;
        }

        $my_vpn =
                "file_name:\n".
                "/etc/openvpn/my-vpn.conf\n".
                "file_content:\n". 
                "client\n".
                "dev tap0\n".
                "proto udp\n".
                "remote $fb_value 1194\n". 
                "resolv-retry infinite\n".
                "nobind\n".
                "persist-key\n". 
                "persist-tun\n".
                "tun-mtu 1500\n".
                "tun-mtu-extra 32\n". 
                "mssfix 1450\n".
                "ca /etc/openvpn/ca.crt\n".
                "cert /etc/openvpn/cert.crt\n". 
                "key /etc/openvpn/key.key\n". 
                "comp-lzo\n";
        $return_string = $return_string."\n".$my_vpn;

        //______ /etc/openvpn/ca.crt  _____
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'ca','AutoSetup.auto_mac_id' => $mac_id)));
        if($q_r != ''){

            $ca =
                "file_name:\n".
                "/etc/openvpn/ca.crt\n".
                "file_content:\n".
                $q_r['AutoSetup']['value']."\n"; 
            $return_string = $return_string."\n".$ca;
        }

        //______ /etc/openvpn/cert.crt  _____
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'cert','AutoSetup.auto_mac_id' => $mac_id)));
        if($q_r != ''){

            $cert =
                "file_name:\n".
                "/etc/openvpn/cert.crt\n".
                "file_content:\n".
                $q_r['AutoSetup']['value']."\n"; 
            $return_string = $return_string."\n".$cert;
        }

        //______ /etc/openvpn/key.key  _____
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'key','AutoSetup.auto_mac_id' => $mac_id)));
        if($q_r != ''){

            $key =
                "file_name:\n".
                "/etc/openvpn/key.key\n".
                "file_content:\n".
                $q_r['AutoSetup']['value']."\n"; 
            $return_string = $return_string."\n".$key;
        }

        //______ /etc/openvpn/start.sh  _____
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'tun_ip','AutoSetup.auto_mac_id' => $mac_id)));
        $tun_ip     = $q_r['AutoSetup']['value'];
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'tun_mask','AutoSetup.auto_mac_id' => $mac_id)));
        $tun_mask     = $q_r['AutoSetup']['value'];
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'tun_broadcast','AutoSetup.auto_mac_id' => $mac_id)));
        $tun_broadcast = $q_r['AutoSetup']['value'];



        if($q_r != ''){

            $tun_detail =
                "file_name:\n".
                "/etc/openvpn/start.sh\n".
                "file_content:\n".
                "#! /bin/sh\n".
                "echo 'Start VPN'\n". 
                "openvpn --rmtun --dev tap0\n". 
                "openvpn --mktun --dev tap0\n". 
                "brctl addbr br-vpn\n". 
                "brctl delif br-lan ath1\n". 
                "brctl addif br-vpn ath1\n". 
                "brctl addif br-vpn tap0\n".
                "ifconfig br-vpn $tun_ip netmask $tun_mask broadcast $tun_broadcast\n"; 
 
            $return_string = $return_string."\n".$tun_detail;
        }
        return $return_string;
    }


     function _return_wireless($mac_id,$modified){

        $return_string = '';

        //_________ Start off with the /etc/config/wireless file ____________
        //__ Settings to check on this one:______________________________________
        //__ 1.) vpn_server _____________________________________________________

        //---Check if there was changes since the last contact on this key----
        $fb_value = $this->_changes_since_last_contact_for($mac_id,'channel',$modified);

        if(! $fb_value){
            return $return_string;
        }

        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'enabled','AutoSetup.auto_mac_id' => $mac_id)));
        $enabled    = $q_r['AutoSetup']['value'];
        if($enabled == true){
            $enabled = '#';
        }else{
            $enabled = '';
        }

        //General settings
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'channel','AutoSetup.auto_mac_id' => $mac_id)));
        $channel    = $q_r['AutoSetup']['value'];
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'power','AutoSetup.auto_mac_id' => $mac_id)));
        $power      = $q_r['AutoSetup']['value'];
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'distance','AutoSetup.auto_mac_id' => $mac_id)));
        $distance   = $q_r['AutoSetup']['value'];

        //Secure SSID
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'secure_ssid','AutoSetup.auto_mac_id' => $mac_id)));
        $secure_ssid= $q_r['AutoSetup']['value'];
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'radius','AutoSetup.auto_mac_id' => $mac_id)));
        $radius     = $q_r['AutoSetup']['value'];
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'secret','AutoSetup.auto_mac_id' => $mac_id)));
        $secret     = $q_r['AutoSetup']['value'];

        //Open SSID
        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => 'open_ssid','AutoSetup.auto_mac_id' => $mac_id)));
        $open_ssid  = $q_r['AutoSetup']['value'];

        $wireless =
                "file_name:\n".
                "/etc/config/wireless\n".
                "file_content:\n". 
                    "\nconfig wifi-device  wifi0\n".
                    "option type     atheros\n".
                    "option channel  $channel\n".
                    "option txpower  19\n".
                    "option distance $distance\n\n".
                    "# REMOVE THIS LINE TO ENABLE WIFI:\n".
                    $enabled."option disabled 1\n\n".
                    "config wifi-iface\n".
                    "option device   wifi0\n".
                    "option network  lan\n".
                    "option mode ap\n".
                    "option ssid '$secure_ssid'\n".
                    "option encryption wpa2\n".
                    "option key $secret\n".
                    "option server $radius\n".
                    "option port 1812\n\n".
                    "config wifi-iface\n".
                    "option device   wifi0\n".
                    "option mode ap\n".
                    "option ssid '$open_ssid'\n\n";
        $return_string = $return_string."\n".$wireless;
        return $return_string;
    }


    function _changes_since_last_contact_for($mac_id,$key,$modified){

        $q_r = $this->AutoSetup->find('first', array('conditions' =>array( 'AutoSetup.description' => $key,'AutoSetup.auto_mac_id' => $mac_id)));
        if($q_r != ''){
            if(strtotime($q_r['AutoSetup']['modified']) >= strtotime($modified)){
                return $q_r['AutoSetup']['value'];
            }
        }
        return false;  //If there was nothing - or no changes, we return false
    }

}
?>
