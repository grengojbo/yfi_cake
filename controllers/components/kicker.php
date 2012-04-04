<?php
class KickerComponent extends Object {

    var $controller;
    var $radclient;

    function initialize(&$controller) {
        // saving the controller reference for later use
        $this->controller =& $controller;
        //---Location of radclient----
        Configure::load('yfi');
        $this->radclient = Configure::read('freeradius.radclient');
        
    }

    function kick($radacct_entry){

        //Check if there is a NAS with this IP
        $nas_ip             = $radacct_entry['nasipaddress'];
        $username           = $radacct_entry['username'];
        $nasportid          = $radacct_entry['nasportid'];
        $framedipaddress    = $radacct_entry['framedipaddress'];
		$device_mac			= $radacct_entry['callingstationid'];
        $nas_mac			= $radacct_entry['calledstationid'];


        //This is a NAT (dynamic client add-on) where we don't care for the $nas_ip
        //_____ CoovaChilli-NAT ______________
        $nat_q_r = $this->controller->Na->find('first',array('conditions' => array('Na.community' => $nas_mac,'Na.type' => 'CoovaChilli-NAT')));

        if($nat_q_r != ''){
            $nas_id = $nat_q_r['Na']['id'];
            $d['Action']['na_id'] = $nas_id;
            $d['Action']['action'] = 'execute';
            $d['Action']['command'] = "chilli_query logout $device_mac";
            $this->controller->Action->save($d);
            return;
        }

        $q_r = $this->controller->Na->findByNasname($nas_ip);

        if($q_r){

			//Check if it is a Device (MAC authenticated)
			$device_flag = $this->_check_for_device($device_mac);

            //Check the type
            $type = $q_r['Na']['type'];
            //======================================================================================
            //=======Different Types of NAS devices Require different type of disconnect actions====
            //======================================================================================
            if(($type == 'CoovaChilli-AP')|($type == 'CoovaChilli')){

                //Check the port of the device's COA
                $port   = $q_r['Na']['ports'];
                $secret = $q_r['Na']['secret'];

                //Send the NAS a POD packet
                //-------------------------------------------
                if($nas_ip == '0.0.0.0'){   //This is a hack for Chillispot since it reports 0.0.0.0
                    $nas_ip='127.0.0.1';
                }
                //Now we can attempt to disconnect the person
                $output = array();
                //Get the location of the radpod script
                // print("Disconnecting $username");
                //You may need to fine-tune the -t and -r switches - See man radclient for more detail
                $rc = $this->radclient;

				//If it is a MAC authenticated device - also send a disconnect command using the device MAC as username
				if($device_flag >= 1){
					exec("echo \"User-Name = $device_mac\" | $rc -r 2 -t 2 $nas_ip:$port 40 $secret",$output);
				} 
                exec("echo \"User-Name = $username\" | $rc -r 2 -t 2 $nas_ip:$port 40 $secret",$output);
                //$this->requestAction("/radaccts/close/$id");
                //----------------------------------------------
            }

             //____ Mikrotik _____ 
    		if($type == 'Mikrotik'){
        		$port   = $q_r['Na']['ports'];
        		$secret = $q_r['Na']['secret'];
        		//Mikrotik requires that we need to know the IP the user comes in with
        		$rc = $this->radclient;

				//If it is a MAC authenticated device - also send a disconnect command using the device MAC as username
				if($device_flag >= 1){
					exec("echo \"Framed-IP-Address=$framedipaddress,User-Name=$device_mac\" | $rc -r 2 -t 2 $nas_ip:$port disconnect $secret",$output);
				} 
        		exec("echo \"Framed-IP-Address=$framedipaddress,User-Name=$username\" | $rc -r 2 -t 2 $nas_ip:$port disconnect $secret",$output);
    		}

            //==========================================================================================
        }
    }

	function _check_for_device($device_mac){

		$count = $this->controller->Device->find('count', array('conditions' =>array('Device.name' => $device_mac)));
		return $count;
	}


}

?>
