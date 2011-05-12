<?
class NotificationsController extends AppController {
    var $name       = 'Notifications';
   
    var $components     = array('SwiftMailer','Formatter');    //Add the Email component
    var $uses           = array('User','Check','Notify','Radacct','NotificationDetail');   //Tables to check for recent changes


    function check_notification(){

        //See when last this script was run -( we do this to only catch the account records'users not notified since latst run)
        $q_r =$this->Check->find('first',array('conditions' => array('Check.name' =>'notify_check')));

        $id;
        if($q_r){
            $id         = $q_r['Check']['id'];
            $modified   = $q_r['Check']['modified'];
            print("________________________________________\n");
            print("__ Notify Check Last run on $modified __\n");
            print("________________________________________\n");
            
            $this->_notify_check($id,$modified);
        }else{
            print("________________________________________\n");
            print("_______ First Notify Check______________\n");
            print("________________________________________\n");
            $this->_notify_check(0);
        }

    }


    function _notify_check($id,$last_time = null){
        if($last_time == null){

            $this->_first_check();
            //Add an entry to the checks table
            $d['Check']['id']       = '';
            $d['Check']['name']     = 'notify_check';
            $d['Check']['value']    = '1';
            $this->Check->save($d);

        }else{

            $this->_re_check($last_time);
            $d['Check']['id']       = $id;
            $d['Check']['name']     = 'notify_check';
            $d['Check']['value']    = '1';
            $this->Check->save($d);

        }
    }


    function _re_check($last_time){
        //Get all the unique usernames in the radacct table
        $q_r = $this->Radacct->find('all', array('fields'=>array('DISTINCT Radacct.username'),'conditions'=> array('Radacct.acctstarttime >=' => $last_time )));
        //Loop each entry and check:
        foreach($q_r as $entry){
            $username = $entry['Radacct']['username'];
            $this->_notify_check_user($username);
        }
    }

    function _first_check(){
        //Get all the unique usernames in the radacct table
        $q_r = $this->Radacct->findAll(null, array('DISTINCT Radacct.username'));
        //Loop each entry and check:
        foreach($q_r as $entry){
            $username = $entry['Radacct']['username'];
            $this->_notify_check_user($username);
        }
    }

    function _notify_check_user($username){
    //Function is given a username from the radacct table that will be checked to see it this is the following:
    // 1.) Is there a permanent user with this name
    // 2.) Does he/she have notification active
    // 3.) What was the last notification send to this user

        print "-> Verify Notification Status for $username ....\n";
        
        $q_r        = $this->User->findByUsername($username);

        //Return if the user is not a permanent user
        if($q_r == ''){
            print "  -> $username is not a permanent user\n";
            return;
        }

        $user_id    = $q_r['User']['id'];
        $data       = $q_r['User']['data'];
        $time       = $q_r['User']['time'];

        //Check to see if there is actually a percentage in the user's record - if not return
        if(($data == 'NA')&&($time == 'NA')){
            print "  -> $username does not have any time or data usage\n";
            return;
        }


        print "-> Check if notification active\n";
        $q_r        = $this->NotificationDetail->findByUserId($user_id);
        if($q_r){

            $type   = $q_r['NotificationDetail']['type'];
            $start  = $q_r['NotificationDetail']['start'];
            $incr   = $q_r['NotificationDetail']['increment'];
            $addr1  = $q_r['NotificationDetail']['address1'];
            $addr2  = $q_r['NotificationDetail']['address2'];

            //--Initialize the swift mailer --- need to do this since we call this controller from a script
            $this->SwiftMailer->startup();

            //---Get last value for notification-----
            $last_notify = $this->_get_last_notification($user_id);

            print("LAST NOTIFY $last_notify\n");

            //Is this the first notification?
            if($last_notify == 0){
                print "-> First Notification\n";
                //Data check
                if(($data != 'NA')&&($data > $start)){
                    $d      = array();
                    $d['Notify']['id']      = '';
                    $d['Notify']['user_id'] = $user_id;
                    $d['Notify']['value']   = $start;
                    $this->Notify->save($d);
                    $last_notify = $start;
                    if($type == 'email'){
                        $this->_send_mail('Data',$last_notify,$addr1,$addr2,$user_id);
                    }
                }
                //Time check
                if(($time != 'NA')&&($time > $start)){
                    $d      = array();
                    $d['Notify']['id']      = '';
                    $d['Notify']['user_id'] = $user_id;
                    $d['Notify']['value']   = $start;
                    $this->Notify->save($d);
                    $last_notify = $start;
                    if($type == 'email'){
                        $this->_send_mail('Time',$last_notify,$addr1,$addr2,$user_id);
                    }
                }
            }

            //Subsequint notifications
            if(($data != 'NA')&&($data > $start)){
                print "-> Subsequent Notification $last_notify $incr $data \n";
                while(($last_notify+$incr) < $data){
                    $d      = array();
                    $d['Notify']['id']      = '';
                    $d['Notify']['user_id'] = $user_id;
                    $d['Notify']['value']   = $last_notify+$incr;
                    $this->Notify->save($d);
                    $last_notify = $last_notify+$incr;
                    if($type == 'email'){
                        $this->_send_mail('Data',$last_notify,$addr1,$addr2,$user_id);
                    }
                }
            }

            if(($time != 'NA')&&($time > $start)){
                print "-> Subsequent Notification $last_notify $incr $data \n";
                while(($last_notify+$incr) < $time){
                    $d      = array();
                    $d['Notify']['id']      = '';
                    $d['Notify']['user_id'] = $user_id;
                    $d['Notify']['value']   = $last_notify+$incr;
                    $this->Notify->save($d);
                    $last_notify = $last_notify+$incr;
                    if($type == 'email'){
                        $this->_send_mail('Time',$last_notify,$addr1,$addr2,$user_id);
                    }
                }
            }
        }
    }

    function _get_last_notification($user_id){

        //Get the last entry
        $q_r = $this->Notify->find('first',array('conditions'=>array('Notify.user_id' => $user_id,'Notify.modified >=' => $this->Formatter->start_of_month()), 'order'=>array('Notify.value DESC'))); //Get the start of the month!
        if($q_r){
            return $q_r['Notify']['value'];
        }else{
            return 0;
        }
    }

    function _send_mail($type,$usage,$to,$bc,$user_id){

        //Get the detail for this permanent user
        $q_r = $this->User->findById($user_id);
       // print_r($q_r);

        $username   = $q_r['User']['username'];
        $from       = $q_r['Realm']['email'];

        Configure::load('yfi');
        if($from == ''){
            $from = Configure::read('email.from');
        }

        //Prepare and send message
        $to_list            = array($to);
        if($bc != ''){
            array_push($to_list,$bc);
        }
        //Prepare and send message
        $this->SwiftMailer->sendMessage($to_list,$from,"Usage report for $username - $type: $usage %","Usage Report on $username");
    }

}
?>