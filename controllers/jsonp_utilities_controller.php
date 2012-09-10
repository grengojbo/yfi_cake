<?
class JsonpUtilitiesController extends AppController {
    var $name       = 'JsonpUtilities';
    var $helpers    = array('Javascript');
    var $uses       = array('User');
    var $components = array('SwiftMailer');    //Add the locker component
    var $scaffold;

    var $freeProfiles = array('Permanet Free Internet');
    var $fastProfiles = array('Permanent Fast Intenet 2M');

    function beforeFilter() {
       $this->Auth->allow('*');
    }


    function isUserRegistered(){

        $this->layout = 'ajax';     
        //http://127.0.0.1/c2/yfi_cake/JsonpUtilities/isUserRegistered?callback=callback&email=dirkvanderwalt@gmail.com

        //Also
        //returnUid=true&profileType=true&includeExpiry=true

        $json_return = array('success' => false);   //Fail it by default

        if(array_key_exists('email',$this->params['url'])){
            $email      = $this->params['url']['email'];
            $q_r        = $this->User->find('count',array('conditions' => array('User.email' => $email)));
            if($q_r === 1){
                $json_return = array('success' => true);  
            }
        }


        //We have here a list 

        if(array_key_exists('username',$this->params['url'])){
            $username   = $this->params['url']['username'];
            $q_r        = $this->User->find('first',array('conditions' => array('User.username' => $username)));
            if($q_r  != ''){
                $json_return = array('success' => true);
            }
            
            //See if we need to return the UID..
            if(array_key_exists('returnUid',$this->params['url'])){
                $json_return['user_id']= $q_r['User']['id'];
            }

            //See if we need to return the email
            if(array_key_exists('returnEmail',$this->params['url'])){
                $json_return['user_email']= $q_r['User']['email'];
            }

            //See if we need to test for Free / Fast
            if(array_key_exists('profileType', $this->params['url'])){  

                $json_return['profile_type']= false;    //Default is false

                $profile = $q_r['Profile']['name'];
                if(in_array($profile,$this->freeProfiles)){
                    $json_return['profile_type']= 'free';
                }

                if(in_array($profile,$this->fastProfiles)){
                    $json_return['profile_type']= 'fast';
                }
            }

            //See if we need to include Expiry date
            if(array_key_exists('includeExpiry', $this->params['url'])){  
                $un = $q_r['User']['username'];
                $e_q = $this->User->Radcheck->find('first',array('conditions' => array('Radcheck.username' => $un,'attribute' => 'Expiration')));

                $json_return['expire']= $e_q['Radcheck']['value'];

            }


        }
        $this->set('json_return',$json_return);

        $callback   = $this->params['url']['callback'];
        $this->set('json_pad_with',$callback);
 
    }

    function sendCredentials(){

        $this->layout = 'ajax';
        $json_return = array('success' => false);   //Fail it by default
        if(array_key_exists('email',$this->params['url'])){
            //Get the detail for this user
            $email      = $this->params['url']['email'];
            $qr = $this->User->find('first',array('fields'=> array('Radcheck.*'),'conditions' => array('User.email' => $email)));
            //Get the values of radcheck....
            if($qr != ''){
                $un = $qr['Radcheck']['username'];
                $pw = $qr['Radcheck']['value'];
                $json_return = array('success' => true);
                $this->SwiftMailer->sendMessage(array($email),'dirkvanderwalt@gmail.com','Credentails for hotspot' ,"Username: $un\nPassword: $pw");
            }else{
                $json_return['success'] = false;
                $json_return['error'] = "User's email not registered";
            }
        }
        $this->set('json_return',$json_return);
        $callback   = $this->params['url']['callback'];
        $this->set('json_pad_with',$callback);
    }

     function ccHash(){

        $x_login    = '5D53Kb7Vam';
        $trans_key  = '69K7f5c5XVGLVa73'; //Keep secret!!!!

        $this->layout = 'ajax';
        $json_return = array();
        $json_return['success'] = true;   //Fail it by default
        $json_return['result']  = array();
        $t = time();
        
        $x_amount = $this->params['url']['x_amount'];
       
        $hash = hash_hmac("md5","$x_login^$t^$t^$x_amount^",$trans_key); 

        $json_return['result']['x_fp_timestamp'] = $t;
        $json_return['result']['x_fp_hash']      = $hash;


        $this->set('json_return',$json_return);
        $callback   = $this->params['url']['callback'];
        $this->set('json_pad_with',$callback);

    }
}
?>
