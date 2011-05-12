<?
class ExtrasController extends AppController {
    var $name       = 'Extras';
    var $helpers    = array('Javascript');

    var $uses       = array('Extra','User','Radacct','Radcheck','Radgroupcheck','Radusergroup','User');

    var $components = array('Session','Rights','Json','Dojolayout','Pptpd','Formatter','CmpPermanent');    //Add the locker component

  //  var $scaffold;

    //------------------------------------------------------------------
    //--- List of rights that can be tweaked ---------------------------
    //------------------------------------------------------------------
    /*
        1.) json_time_add()
        2.) json_data_add()
        3.) json_cap_del()
    */
    //-----------------------------------------------------------------

    function json_data_list($user_id){

        $this->layout = 'ajax';

        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        $date_start_end = $this->_get_month_start_and_end(strtotime("now"));

        $e =   $this->Extra->find('all',array('conditions' => array(
                                                    'Extra.user_id'             => $user_id,
                                                    'Extra.created <='          => $date_start_end[0],
                                                    'Extra.created >='          => $date_start_end[1],
                                                    'Extra.type'                => 'data'
                    )));
        foreach($e as $item){

            array_push($json_return['items'],array(
                                'id'    => $item['Extra']['id'],
                                'name'  => $this->Formatter->formatted_bytes($item['Extra']['value'])
                                ));
        }

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function json_time_list($user_id){

        $this->layout = 'ajax';
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = array();

        $date_start_end = $this->_get_month_start_and_end(strtotime("now"));

        $e =   $this->Extra->find('all',array('conditions' => array(
                                                    'Extra.user_id'             => $user_id,
                                                    'Extra.created <='          => $date_start_end[0],
                                                    'Extra.created >='          => $date_start_end[1],
                                                    'Extra.type'                => 'time'
                    )));
        foreach($e as $item){
            array_push($json_return['items'],array(
                                'id'    => $item['Extra']['id'],
                                'name'  => $this->Formatter->formatted_seconds($item['Extra']['value'])
                                ));
        }
        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

     function json_data_add($id){

        $this->layout = 'ajax';
        $extra_data             = $this->params['form']['bytes'];
        $units                  = $this->params['form']['units'];

        if($units   == 'kb'){

            $extra_data = $extra_data * 1024;
        }

        if($units   == 'mb'){
            $extra_data = ($extra_data * 1024) * 1024;
        }

        if($units == 'gb'){
            $extra_data = ($extra_data * 1024) *1024;
            $extra_data = $extra_data * 1024;
        }

        $d                      = array();
        $d['Extra']['id']       = '';
        $d['Extra']['type']     = 'data';
        $d['Extra']['value']    = $extra_data;
        $d['Extra']['user_id']  = $id;
        $this->Extra->save($d);

        $this->CmpPermanent->update_user_usage($id);


        $json_return['json']['status']      = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_time_add($id){

        $this->layout = 'ajax';

        $extra_time             = $this->params['form']['extra_time'];  //Will be in the formant T00:15:00
        $pieces                 = explode(":", $extra_time);
        $hour                   = preg_replace('/T/', '', $pieces[0]);
        $minute                 = $pieces[1];
        $total_time             = ($hour * 60 * 60)+ ($minute * 60);

        $d                      = array();
        $d['Extra']['id']       = '';
        $d['Extra']['type']     = 'time';
        $d['Extra']['value']    = $total_time;
        $d['Extra']['user_id']  = $id;
        $this->Extra->save($d);

        $this->CmpPermanent->update_user_usage($id);

        $json_return['json']['status']      = 'ok';
        $json_return['json']['time']        = $total_time;
        $this->set('json_return',$json_return);
    }

    function json_cap_del(){
        $this->layout = 'ajax';

        //Get the user_id these caps belong to in order to update the usage after delete
        $id = $this->params['form'][0]; //Get the first ID
        $qr = $this->Extra->findById($id);
        foreach($this->params['form'] as $item){
            $this->Extra->delete($item);
        }
        $this->CmpPermanent->update_user_usage($qr['Extra']['user_id']);
        $json_return['json']['status']    = 'ok';
        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    //*****************************************************************************************************
    //----------- Dynamic Actions -------------------------------------------------------------------------
    //*****************************************************************************************************
 
    function json_actions($user_id){

        //----------------------------------------------------------------------------
        //--Description: Get the actions to display on templates tab----------------- 
        //--AP Sepcific: YES --------------------------------------------------------
        //--Rights Completed:   NA -------------------------------------------------
        //----------------------------------------------------------------------------

        $this->layout                     = 'ajax';

        //See what cap the user has - prepaid caps should exclude the abiltity to add or remove extra caps
        $qr = $this->User->find('first',array('conditions'=> array('User.id' => $user_id)));
        $cap = $qr['User']['cap'];
        if($cap == 'prepaid'){
            $json_return['items']             = $this->Dojolayout->actions_for_prepaid_extras();
        }else{
            $json_return['items']             = $this->Dojolayout->actions_for_extras();
        }
        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);
    }

    function json_actions_permanent_user($user_id){

        $this->layout                     = 'ajax';

        //Ensore our user is asking for him/herself
        $auth_data  = $this->Session->read('AuthInfo');
        if($user_id != $auth_data['User']['id']){
            $this->set('json_return',$this->Json->permFail());
            return;
        }
        $json_return = array();

        $qr = $this->User->find('first',array('conditions'=> array('User.id' => $user_id)));
        $cap = $qr['User']['cap'];
        if($cap == 'prepaid'){
            $json_return['items']             = $this->Dojolayout->actions_for_permanent_prepaid_extras();
        }else{
            $json_return['items']             = $this->Dojolayout->actions_for_permanent_extras();
        }

        $json_return['json']['status']    = 'ok';
        $this->set('json_return',$json_return);

    }


    function _get_month_start_and_end($timestamp){

        Configure::load('yfi');
        $reset_date = Configure::read('permanent_users.reset_day');

        $l_assoc = localtime($timestamp, true);    //Get the components for this date
        //Start of month will be:
        if($l_assoc['tm_mday'] >= $reset_date){   
            $m = $l_assoc['tm_mon']+1;  //Use current month
        }else{
            $m = $l_assoc['tm_mon'];    //Use previous month
        }

        //mktime(hour,minute,second,month,day,year,is_dst) 
        $date_end       = date("Y-m-d H:i:s",mktime(0,0,0,$m,$reset_date,($l_assoc['tm_year']+1900)));          //Start of month
        $date_start     = date("Y-m-d H:i:s",mktime(23,59,59,$m+1,$reset_date-1,($l_assoc['tm_year']+1900)));  //End of month
        return array($date_start,$date_end);
    }



}
?>