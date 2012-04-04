<?php
class ActionsComponent extends Object {

   //This component will determine what will be displayed when logged in - determined by rights
    var $components = array('Session','Rights','Dojolayout'); 

    function actions_for_templates(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        //--------------------------------
        if($auth_data['Group']['name'] == Configure::read('group.admin')){

            array_push($struct,array('name'=> gettext('Reload List'),        'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Edit Selected'),      'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Add Template'),       'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
        }
        //-------------------------------

        //--------------------------------------------
        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            if($this->Dojolayout->_look_for_right('templates/json_edit')){
                array_push($struct,array('name'=> gettext('Edit Selected'),      'type' => 'edit',   'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('templates/json_add')){
                array_push($struct,array('name'=> gettext('Add Template'),       'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('templates/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
            }
        }
        //----------------------------------------------

        return $struct;
    }


     function actions_for_template_view(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');
        array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));

        if($auth_data['Group']['name'] == Configure::read('group.admin')){

            array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            if($this->Dojolayout->_look_for_right('templates/json_attr_delete')){
                array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }

    function actions_for_profiles(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Edit Selected'),      'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Add Profile'),       'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            if($this->Dojolayout->_look_for_right('profiles/json_edit')){
                array_push($struct,array('name'=> gettext('Edit Selected'),      'type' => 'edit',   'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('profiles/json_add')){
                array_push($struct,array('name'=> gettext('Add Profile'),       'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('profiles/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }

     function actions_for_profile_view(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            if($this->Dojolayout->_look_for_right('profiles/json_attribute_delete')){
                array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
            }

        }

        return $struct;
    }

    function actions_for_vouchers(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){

            
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Test Radius'),      'type' => 'test',   'action' => 'test_radius'));
            array_push($struct,array('name'=> gettext('CSV Export'),       'type' => 'csv',    'action' => 'csv'));
            array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Test Radius'),      'type' => 'test',   'action' => 'test_radius'));
            if($this->Dojolayout->_look_for_right('vouchers/csv')){
                array_push($struct,array('name'=> gettext('CSV Export'),       'type' => 'csv',    'action' => 'csv'));
            }
            if($this->Dojolayout->_look_for_right('vouchers/csv')){
                array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            }
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));     //The Edit right is available for all who can view vouchers What they can edit.
                                                                                                                //is further granulartly determined by finer tuned rights
            if($this->Dojolayout->_look_for_right('vouchers/json_add')){
                array_push($struct,array('name'=> gettext('Add'),       'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('vouchers/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }

    function actions_for_voucher_profile(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Change Profile'),  'type' => 'edit_profile', 'action' => 'edit_profile'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            if($this->Dojolayout->_look_for_right('vouchers/json_change_profile')){
                array_push($struct,array('name'=> gettext('Change Profile'),  'type' => 'edit_profile', 'action' => 'edit_profile'));
            }
        }
        return $struct;

    }

     function actions_for_voucher_private(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){

            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit_private'));
            array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add_private'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del_private'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            if($this->Dojolayout->_look_for_right('vouchers/json_edit_private')){
                array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit_private'));
            }

            if($this->Dojolayout->_look_for_right('vouchers/json_add_private')){
                array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add_private'));
            }

            if($this->Dojolayout->_look_for_right('vouchers/json_del_private')){
                array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del_private'));
            }
        }
        return $struct;

    }


     function actions_for_voucher_activity(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload_activity'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del_activity'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload_activity'));
            if($this->Dojolayout->_look_for_right('vouchers/json_del_activity')){
                array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del_activity'));
            }
        }
        return $struct;

    }



    function actions_for_batches(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){

            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('CSV Export'),       'type' => 'csv',    'action' => 'csv'));
            array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            if($this->Dojolayout->_look_for_right('batches/csv')){
                array_push($struct,array('name'=> gettext('CSV Export'),       'type' => 'csv',    'action' => 'csv'));
            }
            if($this->Dojolayout->_look_for_right('batches/pdf')){
                array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            }
            if($this->Dojolayout->_look_for_right('batches/json_view')){
                array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('vouchers/json_add_batch')){
                array_push($struct,array('name'=> gettext('Add'),       'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('batches/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }

     function actions_for_batch_view(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Test Radius'),      'type' => 'test',   'action' => 'test_radius'));
            array_push($struct,array('name'=> gettext('Edit Selected'),      'type' => 'edit', 'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Test Radius'),      'type' => 'test',   'action' => 'test_radius'));
            //Edit = View = always available - the rigths in the view is determined more granular
            array_push($struct,array('name'=> gettext('Edit Selected'),      'type' => 'edit', 'action' => 'edit'));

             if($this->Dojolayout->_look_for_right('profiles/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),    'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }



     function actions_for_nas(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Add VPN connected NAS'), 'type' => 'add_tunnel',    'action' => 'add_tunnel'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
        }

        //print_r($auth_data);

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));

            if($this->Dojolayout->_look_for_right('nas/json_edit')){
                array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('nas/json_add')){ 
                array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('nas/json_add_vpn')){ 
                array_push($struct,array('name'=> gettext('Add VPN connected NAS'), 'type' => 'add_tunnel',    'action' => 'add_tunnel'));
            }
            if($this->Dojolayout->_look_for_right('nas/json_del')){ 
                array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }

    function actions_for_nas_view(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
        }

        //print_r($auth_data);

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            if($this->Dojolayout->_look_for_right('nas/json_del')){ 
                array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }



      function actions_for_activity(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Kick User Off'),    'type' => 'kick',   'action' => 'kick'));
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            if($this->Dojolayout->_look_for_right('radaccts/json_kick_users_off')){
                array_push($struct,array('name'=> gettext('Kick User Off'),    'type' => 'kick',   'action' => 'kick'));
            }
           array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));      //AP's can always view but granular rights are set on the view of the Permanent User/ Voucher
        }
        return $struct;
    }


     function actions_for_permanent_users(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){

           array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
           array_push($struct,array('name'=> gettext('Add Note'),         'type' => 'note',   'action' => 'note'));
           array_push($struct,array('name'=> gettext('Change Password'),  'type' => 'password', 'action' => 'password'));
           array_push($struct,array('name'=> gettext('Activate/Disable Selected'), 'type' => 'disable', 'action' => 'disable'));
           array_push($struct,array('name'=> gettext('Send Message'),     'type' => 'message','action' => 'message'));
           array_push($struct,array('name'=> gettext('Test Radius'),      'type' => 'test',   'action' => 'test_radius'));
          // array_push($struct,array('name'=> 'CSV Export',       'type' => 'csv',    'action' => 'csv'));
           array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
           array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
           array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
        }

        
        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));     //AP's can always reload list
            array_push($struct,array('name'=> gettext('Add Note'),         'type' => 'note',   'action' => 'note'));

            if($this->Dojolayout->_look_for_right('permanent_users/json_password')){
                array_push($struct,array('name'=> gettext('Change Password'),  'type' => 'password', 'action' => 'password'));
            }
            if($this->Dojolayout->_look_for_right('permanent_users/json_disable')){
                array_push($struct,array('name'=> gettext('Activate/Disable Selected'), 'type' => 'disable', 'action' => 'disable'));
            }
           
            if($this->Dojolayout->_look_for_right('permanent_users/json_send_message')){
                array_push($struct,array('name'=> gettext('Send Message'),     'type' => 'message','action' => 'message'));
            }
            if($this->Dojolayout->_look_for_right('permanent_users/json_test_auth')){
                array_push($struct,array('name'=> gettext('Test Radius'),      'type' => 'test',   'action' => 'test_radius'));
            }
            if($this->Dojolayout->_look_for_right('permanent_users/csv')){
              //  array_push($struct,array('name'=> 'CSV Export',       'type' => 'csv',    'action' => 'csv'));
            }
            if($this->Dojolayout->_look_for_right('permanent_users/json_edit')){
                array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('permanent_users/json_add')){
                array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('permanent_users/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
            }
           
        }
        
        return $struct;
    }


    function actions_for_user_profile(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Change Profile'),  'type' => 'edit_profile', 'action' => 'edit_profile'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            if($this->Dojolayout->_look_for_right('permanent_users/json_change_profile')){
                array_push($struct,array('name'=> gettext('Change Profile'),  'type' => 'edit_profile', 'action' => 'edit_profile'));
            }
        }
        return $struct;
    }

    function actions_for_user_private(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){

            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit_private'));
            array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add_private'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del_private'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            if($this->Dojolayout->_look_for_right('permanent_users/json_edit_private')){
                array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit_private'));
            }

            if($this->Dojolayout->_look_for_right('permanent_users/json_add_private')){
                array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add_private'));
            }

            if($this->Dojolayout->_look_for_right('permanent_users/json_del_private')){
                array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del_private'));
            }
        }
        return $struct;
    }

     function actions_for_user_activity(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload_activity'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del_activity'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload_activity'));
            if($this->Dojolayout->_look_for_right('permanent_users/json_del_activity')){
                array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del_activity'));
            }
        }
        return $struct;
    }



     function actions_for_extras(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){

           array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
           array_push($struct,array('name'=> gettext('Remove Time'),      'type' => 'del_time','action' => 'del_time'));
           array_push($struct,array('name'=> gettext('Add Time'),         'type' => 'add_time','action' => 'add_time'));
           array_push($struct,array('name'=> gettext('Remove Data'),      'type' => 'del_data','action' => 'del_data'));
           array_push($struct,array('name'=> gettext('Add Data'),         'type' => 'add_data','action' => 'add_data'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));     //AP's can always reload list
            if($this->Dojolayout->_look_for_right('extras/json_cap_del')){
                array_push($struct,array('name'=> gettext('Remove Time'),      'type' => 'del_time','action' => 'del_time'));
            }
            if($this->Dojolayout->_look_for_right('extras/json_time_add')){
                array_push($struct,array('name'=> gettext('Add Time'),         'type' => 'add_time','action' => 'add_time'));
            }
            if($this->Dojolayout->_look_for_right('extras/json_cap_del')){
                array_push($struct,array('name'=> gettext('Remove Data'),      'type' => 'del_data','action' => 'del_data'));
            }
            if($this->Dojolayout->_look_for_right('extras/json_data_add')){
                array_push($struct,array('name'=> gettext('Add Data'),         'type' => 'add_data','action' => 'add_data'));
            }
        }
        return $struct;
    }

    function actions_for_prepaid_extras(){

        //Prepaid users will not have to add caps
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');
        array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
        return $struct;
    }

    //-- 11-3-10 Self Service add on ---
    function actions_for_permanent_extras(){
        $struct     =   array();
        Configure::load('yfi');
        array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));

        //-- Add Time --
        if($this->Dojolayout->_look_for_right('usage/add_time')){
            array_push($struct,array('name'=> gettext('Add Time'),  'type' => 'add_time','action' => 'add_time'));
        }

        //-- Add Data --
        if($this->Dojolayout->_look_for_right('usage/add_data')){
            array_push($struct,array('name'=> gettext('Add Data'),         'type' => 'add_data','action' => 'add_data'));
        }

        return $struct;
    }


    function actions_for_permanent_prepaid_extras(){
        $struct     =   array();
        Configure::load('yfi');
        array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
        return $struct;
    }

    //-- END Self Service add on --


    function actions_for_billing_plans(){

        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
           // array_push($struct,array('name'=> gettext('Help'),             'type' => 'help',   'action' => 'help'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){

            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));     //AP's can always reload list
            if($this->Dojolayout->_look_for_right('billing_plans/json_edit')){
                array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('billing_plans/json_add')){
                array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('billing_plans/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
            }
           // array_push($struct,array('name'=> gettext('Help'),             'type' => 'help',   'action' => 'help'));
        }
        return $struct;
    }

     function actions_for_accnts(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Add Note'),         'type' => 'note',   'action' => 'note'));
            array_push($struct,array('name'=> gettext('Add Payment'),      'type' => 'payment','action' => 'payment'));
            array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            array_push($struct,array('name'=> gettext('Send e-Mail'),      'type' => 'mail',   'action' => 'mail'));
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Create Invoices'),  'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Invoices'),  'type' => 'delete', 'action' => 'del'));
            //array_push($struct,array('name'=> gettext('Help'),             'type' => 'help',   'action' => 'help'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));     //AP's can always reload list
            array_push($struct,array('name'=> gettext('Add Note'),         'type' => 'note',   'action' => 'note'));
            if($this->Dojolayout->_look_for_right('accnts/json_payment_add')){
                array_push($struct,array('name'=> gettext('Add Payment'),      'type' => 'payment','action' => 'payment'));
            }
            array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            array_push($struct,array('name'=> gettext('Send e-Mail'),      'type' => 'mail',   'action' => 'mail'));
            if($this->Dojolayout->_look_for_right('accnts/json_index')){    //If can view - can open for edit (may not be able to do anything on open for edit)
                array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('accnts/json_add')){    //If can view - can open for edit (may not be able to do anything on open for edit)
                array_push($struct,array('name'=> gettext('Create Invoices'),  'type' => 'add',    'action' => 'add'));
            }
             if($this->Dojolayout->_look_for_right('accnts/json_del_invoice')){    //If can view - can open for edit (may not be able to do anything on open for edit)
                array_push($struct,array('name'=> gettext('Delete Invoices'),  'type' => 'delete', 'action' => 'del'));
            }
           // array_push($struct,array('name'=> gettext('Help'),             'type' => 'help',   'action' => 'help'));
        }



        return $struct;
    }


     function actions_for_accnt_invoices(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            array_push($struct,array('name'=> gettext('Send e-Mail'),      'type' => 'mail',   'action' => 'mail'));
            array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Invoices'),  'type' => 'delete', 'action' => 'del'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            array_push($struct,array('name'=> gettext('Send e-Mail'),      'type' => 'mail',   'action' => 'mail'));
            if($this->Dojolayout->_look_for_right('accnts/json_add')){
                array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('accnts/json_del_invoice')){    
                array_push($struct,array('name'=> gettext('Delete Invoices'),  'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }


     function actions_for_accnt_payments(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            array_push($struct,array('name'=> gettext('Send e-Mail'),      'type' => 'mail',   'action' => 'mail'));
            array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Payments'),  'type' => 'delete', 'action' => 'del'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Generate PDF'),     'type' => 'pdf',    'action' => 'pdf'));
            array_push($struct,array('name'=> gettext('Send e-Mail'),      'type' => 'mail',   'action' => 'mail'));
            if($this->Dojolayout->_look_for_right('accnts/json_payment_add')){
                array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('accnts/json_del_payment')){    
                array_push($struct,array('name'=> gettext('Delete Payments'),  'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }

     function actions_for_extra_services(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            if($this->Dojolayout->_look_for_right('extra_services/json_edit')){
                array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('extra_services/json_add')){
                array_push($struct,array('name'=> gettext('Add'),              'type' => 'add',    'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('extra_services/json_del')){    
                array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
            }
        }
        return $struct;
    }

    
    function actions_for_access_points(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Reboot AP'),        'type' => 'power', 'action' => 'power'));
        }
        return $struct;
    }

    function actions_for_rogues(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Edit Selected'),    'type' => 'edit',   'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
        }
        return $struct;
    }

    
    function actions_for_clients(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
        }
        return $struct;
    }


    function actions_for_credits(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      	'type' => 'reload', 'action' => 'reload'));
			array_push($struct,array('name'=> gettext('Attach to user'),   	'type' => 'attach', 'action' => 'attach'));
			array_push($struct,array('name'=> gettext('Edit'),   			'type' => 'edit', 'action' => 'edit'));
			array_push($struct,array('name'=> gettext('Add'),      		 	'type' => 'add', 'action' => 'add'));
			array_push($struct,array('name'=> gettext('Delete Selected'),  	'type' => 'delete', 'action' => 'del'));
        }

        //--------------------------------------------
        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            if($this->Dojolayout->_look_for_right('credits/json_attach')){
                array_push($struct,array('name'=> gettext('Attach to user'),    'type' => 'attach', 'action' => 'attach'));
            }
            if($this->Dojolayout->_look_for_right('credits/json_edit')){
                array_push($struct,array('name'=> gettext('Edit'),              'type' => 'edit', 'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('credits/json_add')){
                array_push($struct,array('name'=> gettext('Add'),               'type' => 'add', 'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('credits/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),   'type' => 'delete', 'action' => 'del'));
            }
        }
        //----------------------------------------------
        return $struct;
    }

    
    function actions_for_maps(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),       'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Preferences'),       'type' => 'settings', 'action' => 'settings'));
            array_push($struct,array('name'=> gettext('Edit'),              'type' => 'edit', 'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Add'),               'type' => 'add', 'action' => 'add'));
            array_push($struct,array('name'=> gettext('Delete Selected'),   'type' => 'delete', 'action' => 'del'));
        }
        
        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));      //Everyone can reload
            array_push($struct,array('name'=> gettext('Preferences'),      'type' => 'settings', 'action' => 'settings'));  //Everyone can preferences

            if($this->Dojolayout->_look_for_right('nas/json_edit')){
                array_push($struct,array('name'=> gettext('Edit'),              'type' => 'edit', 'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('nas/json_edit')){         //Adding a marker is editing the NAS
                array_push($struct,array('name'=> gettext('Add'),               'type' => 'add', 'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('nas/json_edit')){        //Deleting a marker is editing the NAS
                array_push($struct,array('name'=> gettext('Delete Selected'),   'type' => 'delete', 'action' => 'del'));
            }
        }


         if($auth_data['Group']['name'] == Configure::read('group.user')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));      //Everyone can reload
            array_push($struct,array('name'=> gettext('Preferences'),      'type' => 'settings', 'action' => 'settings'));  //Everyone can preferences
        }
        
        //----------------------------------------------
        return $struct;
    }




     function actions_for_user_credits(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),       'type' => 'reload', 'action' => 'reload'));
            array_push($struct,array('name'=> gettext('Attach to user'),    'type' => 'attach', 'action' => 'attach'));
            array_push($struct,array('name'=> gettext('Edit'),              'type' => 'edit', 'action' => 'edit'));
            array_push($struct,array('name'=> gettext('Delete Selected'),   'type' => 'delete', 'action' => 'del'));
        }

        //--------------------------------------------
        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            if($this->Dojolayout->_look_for_right('credits/json_attach')){
                array_push($struct,array('name'=> gettext('Attach to user'),    'type' => 'attach', 'action' => 'attach'));
            }
            if($this->Dojolayout->_look_for_right('credits/json_edit')){
                array_push($struct,array('name'=> gettext('Edit'),              'type' => 'edit', 'action' => 'edit'));
            }
            if($this->Dojolayout->_look_for_right('credits/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),   'type' => 'delete', 'action' => 'del'));
            }
        }
        //----------------------------------------------
        return $struct;
    }

    function actions_for_devices(){
        $auth_data = $this->Session->read('AuthInfo');
        $struct     =   array();
        Configure::load('yfi');
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
			array_push($struct,array('name'=> gettext('Add'),      			'type' => 'add', 'action' => 'add'));
			 array_push($struct,array('name'=> gettext('Delete Selected'),  'type' => 'delete', 'action' => 'del'));
        }

        //--------------------------------------------
        if($auth_data['Group']['name'] == Configure::read('group.user')){
            array_push($struct,array('name'=> gettext('Reload List'),      'type' => 'reload', 'action' => 'reload'));
            
            if($this->Dojolayout->_look_for_right('devices/json_add')){
                array_push($struct,array('name'=> gettext('Add'),               'type' => 'add', 'action' => 'add'));
            }
            if($this->Dojolayout->_look_for_right('devices/json_del')){
                array_push($struct,array('name'=> gettext('Delete Selected'),   'type' => 'delete', 'action' => 'del'));
            }
        }
        //----------------------------------------------


        return $struct;
    }


}

?>
