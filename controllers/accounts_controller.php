<?
class AccountsController extends AppController {
    var $name       = 'Accounts';

    var $uses           = array('User','Radacct','Na','Device');   //Tables to check for recent changes
    var $components     = array('Formatter','Kicker');    //Add the locker component
    var $users_group;


    function month_end_close(){

        print("________________________________________\n");
        print("__ Kick off all permanent users ________\n");
        print("________________________________________\n");

        //---- We only list group type 'users'-----------------
        Configure::load('yfi');
        $users_group = Configure::read('group.user');
        $this->users_group = $users_group;
        //-------------END Search Filter --------------------------------

        //Get the end of the month
        $month_end      = $this->Formatter->end_of_month();
        //$unix_month_end = strtotime('2009-06-07 06:03:59');
        $unix_month_end = strtotime($month_end);     //Get the unix stamp for given date
        //Check if now is less than half an hour before month end
        $unix_now       = strtotime('now');
        if($unix_now > ($unix_month_end - 1800)){
            print "Less than half an hour before month end\n";
            //Since we call this from a script - we need to initialize some components
            $this->Kicker->initialize($this);
            $this->_kick_active_permanent_users();
        }
        print "Month end is $month_end ($unix_month_end)\n";
    }


    function month_start_reset(){

        print("________________________________________\n");
        print("__ Kick off all permanent users ________\n");
        print("__ Zero usage fo permanent user_________\n");
        print("________________________________________\n");

        //---- We only list group type 'users'-----------------
        Configure::load('yfi');
        $users_group = Configure::read('group.user');
        $this->users_group = $users_group;
        //-------------END Search Filter --------------------------------

        //Get the end of the month
        $month_start      = $this->Formatter->start_of_month();
        //$unix_month_start = strtotime('2009-06-08 06:00:00');
        $unix_month_start = strtotime($month_start);     //Get the unix stamp for given date
        //Check if now is less than half an hour after month start
        $unix_now       = strtotime('now');
        if($unix_now < ($unix_month_start + 1800)){
            print "Less than half an hour after month start\n";
            //Reset all Permanent users account's usage is not "NA";
            $this->_reset_permanent_user_usage();
            //Since we call this from a script - we need to initialize some components
            $this->Kicker->initialize($this);
            $this->_kick_active_permanent_users();
        }
        print "Month start is $month_start ($unix_month_start)\n";
    }


    function _reset_permanent_user_usage(){

        $q_r    = $this->User->find('all',array('conditions' => array('Group.name'=> $this->users_group, 'User.type <>' => 'prepaid')));
        foreach($q_r as $item){

            $data   = $item['User']['data'];
            $time   = $item['User']['time'];
            $id     = $item['User']['id'];

            if($data != 'NA'){
                $data = '0.00';
            }

            if($time != 'NA'){
                $time = '0.00';
            }

            $d      = array();
            $d['User']['id']            = $id;
            $d['User']['data']          = $data;
            $d['User']['time']          = $time;
            $this->User->save($d);
        }
    }


    function _kick_active_permanent_users(){

        //Get a list of all active connections
        $q_r    = $this->Radacct->find('all',array('conditions' => array('Radacct.acctstoptime' =>null)));
        foreach($q_r as $entry){
            $username   = $entry['Radacct']['username'];
            //Get the IP of the NAS device
            if($this->_check_if_permanent_user($username)>0){
                print "Kick off user $username\n";
                $this->Kicker->kick($entry['Radacct']);
            }

        }
    }

    function _check_if_permanent_user($username){
        $count = $this->User->find('count',array('conditions' => array('User.username' => $username,'Group.name'=> $this->users_group)));
        return $count;
    }

}
?>