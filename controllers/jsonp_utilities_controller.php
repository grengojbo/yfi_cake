<?
class JsonpUtilitiesController extends AppController {
    var $name       = 'JsonpUtilities';
    var $helpers    = array('Javascript');
    var $uses       = array('User','Radusergroup','ExpiryChange');
    var $components = array('SwiftMailer','CmpPermanent');    //Add the locker component
    var $scaffold;

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

                Configure::load('yfi');
                $freeProfiles = Configure::read('profiles.free');
                $fastProfiles = Configure::read('profiles.fast');

                $profile = $q_r['Profile']['name'];
                if(in_array($profile,$freeProfiles)){
                    $json_return['profile_type']= 'free';
                }

                if(in_array($profile,$fastProfiles)){
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

    function freeToFast(){
    
        $this->layout = 'ajax';
        $json_return = array();
        $json_return['success'] = true;   //Fail it by default
        

        $un = $this->params['url']['username'];
        $q_r    = $this->User->find('first',array('conditions' => array('User.username' => $un)));

        $json_return['username']  = $q_r['User']['username'];
        $json_return['email']     = $q_r['User']['email'];
        $json_return['id']        = $q_r['User']['id'];

        $this->changeToFast($json_return['id'],$un);
        $e_q = $this->User->Radcheck->find('first',
            array('conditions' => 
                        array(  'Radcheck.username' => $un,
                                'attribute'         => 'Expiration')
        ));

        //Do the expire thing (now plus one hour...)
        $new_exp = time()+ 3600;
        $initiator  = $this->User->findByUsername('CreditCardProvider');
        $ini_id     = $initiator['User']['id'];

        //Update the expiry date
        $exp_q  = $this->User->Radcheck->find('first', 
                    array('conditions'=>array('username' => $un,'attribute' => 'Expiration'))
                );

        //See if an expiry attribute is specified
        if($exp_q != ''){
            //check if the value changed
            $old_exp = $exp_q['Radcheck']['value'];
            $exp_q['Radcheck']['value'] = $new_exp;
            $this->User->Radcheck->save($exp_q);
            //Record this change....
            $e_ch['ExpiryChange']['old_value']      = $old_exp;
            $e_ch['ExpiryChange']['new_value']      = $new_exp;
            $e_ch['ExpiryChange']['user_id']        = $json_return['id'];
            $e_ch['ExpiryChange']['initiator_id']   = $ini_id;
            $this->ExpiryChange->save($e_ch);          
        }else{
            //Expire 1/1/2017
            $this->_add_entry('Radcheck',$username,'Expiration',$new_exp);
        }

        $json_return['expire']= $new_exp;

        $this->set('json_return',$json_return);
        $callback   = $this->params['url']['callback'];
        $this->set('json_pad_with',$callback);
    }

    function changeToFast($user_id,$username){

        Configure::load('yfi');
        $upgradeName = Configure::read('profiles.upgrade_to');

        $profile    = $this->User->Profile->findByName($upgradeName);
        $profile_id = $profile['Profile']['id']; 

        $this->Radusergroup->removeUser($username);

        //Add the new profile binding
        $this->_add_radusergroup($username,$upgradeName);

        //Update the user with the new profile id
        $this->User->id = $user_id;
        $this->User->saveField('profile_id', $profile_id);

       // $this->CmpPermanent->update_user_usage($user_id);

        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);

    }

    function _add_radusergroup($username,$groupname){

        $this->Radusergroup->id =false;
        $rc = array();
        $rc["Radusergroup"]['username']   = $username;
        $rc["Radusergroup"]['groupname']  = $groupname;
        $rc["Radusergroup"]['priority']   = '1';
        $this->Radusergroup->save($rc);
    }
}
?>
