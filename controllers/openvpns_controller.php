<?php

class OpenvpnsController extends AppController {
    var $name       = 'Openvpns';
    var $helpers    = array('Javascript');

    var $components = array('Session','Dojolayout','Rights','Json');    //Add the locker component
    var $uses       = array('Na');

    function beforeFilter() {
       $this->Auth->allow("*");
    }

    function active_check($mac){
        $this->layout = 'text';
        //Check if the MAC is in the correct format
        $pattern = '/^([0-9a-fA-F]{2}[-]){5}[0-9a-fA-F]{2}$/i';
        if(preg_match($pattern, $mac)< 1){
            $error = "ERROR: MAC missing or wrong";
            $this->set('error',$error);
            return;
        }

        //We use a convention of 08-00-27-56-22-0B_ce5b65b780ae42d937294b2aa166d608 in the community field
        //in the nas table. We specify a MAC with an underscore initially and when the device register itself
        //We add the device generated password
        $r = $this->Na->find('count',array('conditions' =>array('Na.community LIKE' => "%$mac"."_%")));

        if($r == 0){
            $return_string = "OPENVPN=NO";
        }else{
            $return_string = "OPENVPN=YES";
        }

        //--Return the actions (if required) ---
        $this->set('return_string',$return_string);
        //--------------------------------------
    }

    function register_device(){
        $this->layout = 'text';
        if((isset($this->params['url']['mac']))&&(isset($this->params['url']['pwd']))){
            $mac = $this->params['url']['mac'];
            $pwd = $this->params['url']['pwd'];
            //Ensure the device is not already registered
            $q_r = $this->Na->find('first',array('conditions' =>array('Na.community LIKE' => "%$mac"."_%")));
            if($q_r == ''){
                $this->set('return_string','Unknown Device');
                return;
            }
            $community = $q_r['Na']['community'];
            //A device alredy registered will have this field filled with 50 chars
            if(strlen($community)== 50){
            
                $this->set('return_string',"Device already registered");
            }else{
                $this->Na->id = $q_r['Na']['id'] ;
                $this->Na->saveField('community',$mac.'_'.$pwd);

                //Create a file with fixed ip for this device
                $dir = '/etc/openvpn/ccd/';
                $ip  = $q_r['Na']['nasname'];
                $pieces = explode(".", $ip);
                $pieces[3] = $pieces[3]+1;
                $server_ip = implode('.',$pieces);
                $file = fopen("$dir".$mac,"w");
                fwrite($file,"ifconfig-push $ip $server_ip");
                fclose($file);
                $this->set('return_string',"IP=$ip");
            }
        }
    }

}
?>
