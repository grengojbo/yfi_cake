<?php
class LeftpaneComponent extends Object {

   //This component will determine what will be displayed when logged in - determined by rights
    var $components = array('Session','Rights','Dojolayout'); 

    function display(){

        //start with the structure the group of the user will have
        $auth_data = $this->Session->read('AuthInfo');

        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){

            //-----Admin has all the rights--------------------
            return $this->_admin_strucuture();
            //return $this->_ap_structure();
        }

         if($auth_data['Group']['name'] == Configure::read('group.ap')){

            //--AP's has specified rights
            return $this->_ap_structure();

        }

        if($auth_data['Group']['name'] == Configure::read('group.user')){
            $struct = array();
            if($auth_data['User']['cap'] == 'prepaid'){
                return $this->_prepaid_user_structure();
            }else{
                return $this->_user_structure();
            }
            return $struct;
        }
    }

   
    function _admin_strucuture(){

            $struct = array();

            $struct[gettext('Vouchers')]         = array(
                                            'img' => 'voucher.jpg',
                                            'links' => array( gettext('Voucher Management')  => '/actions/VouchersManage', gettext('Batch Management') => '/actions/BatchesManage')
                                        );

            $struct[gettext('Permanent Users')]  = array(
                                            'img' => 'user.jpg',
                                            'links' => array(gettext('User Management')       => '/actions/UsersManage', gettext('Internet Credit')       => '/actions/Credits')
                                        );

           $struct[gettext('Accounting')]  = array(
                                            'img' => 'accounts.png',
                                            'links' => array(gettext('Account Management')       => '/actions/Accounts',gettext('Billing Plans') => '/actions/Plans')
                                        );

            if(Configure::read('experimental.active') == true){

                $struct[gettext('Realms & Providers')]  = array(
                                            'img' => 'realm.jpg',
                                            'links' => array(gettext('Realm Management')    => '/actions/RealmsManage',gettext('Nas Devices')    => '/actions/NasManage', gettext('Access Providers') => '/actions/APManage', gettext('Auto Setup') => '/actions/AutoSetup', gettext('Google Maps') => '/actions/Maps')
                                        );
            }else{
                $struct[gettext('Realms & Providers')]  = array(
                                            'img' => 'realm.jpg',
                                            'links' => array(gettext('Realm Management')    => '/actions/RealmsManage',gettext('Nas Devices')    => '/actions/NasManage', gettext('Access Providers') => '/actions/APManage', gettext('Google Maps') => '/actions/Maps')
                                        );
            }

            $struct[gettext('Profiles')]         = array(
                                            'img' => 'profiles.png',
                                            'links' => array(gettext('Profile Templates')    => '/actions/Templates', gettext('Specific Profiles') => '/actions/Profiles')
                                          //  'links' => array('Profile Templates'    => '/actions/Templates', 'Specific Profiles' => '/actions/Profiles','Dynamic Firewall' => '/actions/Firewall')
                                        );

            if(Configure::read('experimental.active') == true){

                $struct[gettext('Activity/Stats')]   = array(
                                            'img' => 'activity.png',
                                            'links' => array(gettext('Activity Viewer')       => '/actions/Activity', gettext('NAS Usage') => '/actions/StatsNas', gettext('Realm Usage') => '/actions/StatsRealm', gettext('User Usage') => '/actions/StatsUser',gettext('Access Points') => '/actions/AccessPoints')
                                        );
            }else{

                $struct[gettext('Activity/Stats')]   = array(
                                            'img' => 'activity.png',
                                            'links' => array(gettext('Activity Viewer')       => '/actions/Activity', gettext('NAS Usage') => '/actions/StatsNas', gettext('Realm Usage') => '/actions/StatsRealm', gettext('User Usage') => '/actions/StatsUser')
                                        );


            }
            
            return $struct;
    }

    function _ap_structure(){

        $struct = array();
        //-----------------------------------------------------------
        //---- Rights required --------------------------------------
        //---- 'vouchers/json_index'         -----------------------------
        //---- 'permanent_users/json_index'    ---------------------------
        //-----------------------------------------------------------

        //----------------------------------------------
        //----Vouchers----------------------------------
        //----------------------------------------------
        if($this->Dojolayout->_look_for_right('vouchers/json_index')){

            $links_array = array();
            $links_array[gettext('Voucher Management')] = '/actions/VouchersManage';
            if($this->Dojolayout->_look_for_right('batches/json_index')){
                $links_array[gettext('Batch Management')] = '/actions/BatchesManage';
            }

            $struct[gettext('Vouchers')]         = array(
                                                'img'   => 'voucher.jpg',
                                                'links' => $links_array
                                        );
        }

        

        if($this->Dojolayout->_look_for_right('permanent_users/json_index')){

            $struct[gettext('Permanent Users')]  = array(
                                                'img' => 'user.jpg',
                                                'links' => array(gettext('User Management')       => '/actions/UsersManage')
                                            );
            //See if they can do Internet credits
            if($this->Dojolayout->_look_for_right('credits/json_index')){

                $struct[gettext('Permanent Users')]['links'][gettext('Internet Credit')] = '/actions/Credits';
            }
        }

        if($this->Dojolayout->_look_for_right('accnts/json_index')){
            $struct[gettext('Accounting')]  = array(
                                            'img' => 'accounts.png',
                                            'links' => array(gettext('Account Management')      => '/actions/Accounts',gettext('Billing Plans') => '/actions/Plans')
                                        );
        }



        if($this->Dojolayout->_look_for_right('nas/json_index')){

            if(Configure::read('maps.access_providers') == true){    //Do we want to display the maps to Access Providers?
                 $struct[gettext('Realms & Providers')]  = array(
                                                'img' => 'realm.jpg',
                                                'links' => array(gettext('Nas Devices')    => '/actions/NasManage',gettext('Google Maps') => '/actions/Maps')
                                            );
            }else{
                 $struct[gettext('Realms & Providers')]  = array(
                                                'img' => 'realm.jpg',
                                                'links' => array(gettext('Nas Devices')    => '/actions/NasManage')
                                            );
            }
        }
        //----------------------------------------------
        //----Profiles-(always present)-----------------
        //----------------------------------------------
        $profiles_links = array();
        if($this->Dojolayout->_look_for_right('templates/json_index')){ //Right to view templates can be taken away
            $profiles_links[gettext('Profile Templates')] = '/actions/Templates';
        }
        $profiles_links[gettext('Specific Profiles')] = '/actions/Profiles';

        if($this->Dojolayout->_look_for_right('profiles/json_index')){
            $struct[gettext('Profiles')]  = array(
                                                'img' => 'profiles.png',
                                                'links' => $profiles_links
                                        );
        }
        //-----------------------------------------------
        
        if($this->Dojolayout->_look_for_right('radaccts/json_show_active')){
            $struct[gettext('Activity/Stats')]   = array(
                                            'img' => 'activity.png',
                                             'links' => array(gettext('Activity Viewer')       => '/actions/Activity', gettext('NAS Usage') => '/actions/StatsNas', gettext('Realm Usage') => '/actions/StatsRealm', gettext('User Usage') => '/actions/StatsUser')
                                        );
        }
        //----------------------------------------
        return $struct;

    }


     function _user_structure(){

        $struct = array();

         if(Configure::read('maps.permanent_users') == true){    //Do we want to display the maps to Access Providers?
                $struct[gettext('User Detail')]  = array(
                                                'img' => 'user.jpg',
                                                'links' => array(gettext('General Detail')=> '/actions/PermanentGeneral', gettext('Invoices') => '/actions/PermanentInvoices', gettext('Payments') => '/actions/PermanentPayments',gettext('Google Maps') => '/actions/Maps')
                                            );

            }else{
                $struct[gettext('User Detail')]  = array(
                                                'img' => 'user.jpg',
                                                'links' => array(gettext('General Detail')=> '/actions/PermanentGeneral', gettext('Invoices') => '/actions/PermanentInvoices', gettext('Payments') => '/actions/PermanentPayments')
                                            );
            }
        return $struct;
    }


    function _prepaid_user_structure(){
        $struct = array();

        if(Configure::read('maps.permanent_users') == true){    //Do we want to display the maps to Access Providers?
               $struct[gettext('User Detail')]  = array(
                                                'img' => 'user.jpg',
                                                'links' => array(gettext('General Detail')=> '/actions/PermanentGeneral',gettext('Google Maps') => '/actions/Maps')
                                            );

        }else{
              $struct[gettext('User Detail')]  = array(
                                                'img' => 'user.jpg',
                                                'links' => array(gettext('General Detail')=> '/actions/PermanentGeneral')
                                            );
        }
        return $struct;
    }

}



?>
