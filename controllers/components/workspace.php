<?php
class WorkspaceComponent extends Object {

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
            return $this->_user_structure();
        }
    }

   
    function _admin_strucuture(){

            $struct = array();

            //Home tab
             array_push($struct, array( 'eventToSubscribe'  => '/actions/Homepage',
                                        'tabToCreate'       => 'contentWorkspaceHome',
                                        'tabTitle'          => gettext('Home'),
                                        'closable'          =>  false,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Homepage',
                                        'file'              => 'Homepage',
                                        'style'             => 'homeTab'
                            ));


            //Vouchers Manage Grid
             array_push($struct, array( 'eventToSubscribe'  => '/actions/VouchersManage',
                                        'tabToCreate'       => 'contentWorkspaceVouchers',
                                        'tabTitle'          => gettext('Vouchers'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Vouchers',
                                        'file'              => 'Vouchers',
                                        'style'             => 'voucherTab'
                            ));

            //Voucher View
             array_push($struct, array( 'eventToSubscribe'  => '/actions/VoucherView',
                                        'tabToCreate'       => 'contentWorkspaceVoucher',
                                        'tabTitle'          => gettext('Voucher'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.VoucherView',
                                        'file'              => 'VoucherView',
                                        'style'             => 'voucherTab'
                            ));

            //Voucher Batches Manage Grid
            array_push($struct, array(  'eventToSubscribe'  => '/actions/BatchesManage',
                                        'tabToCreate'       => 'contentWorkspaceBatches',
                                        'tabTitle'          => gettext('Batches'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Batches',
                                        'file'              => 'Batches',
                                        'style'             => 'voucherTab'
                            ));

            //Voucher Batches View Batch
            array_push($struct, array(  'eventToSubscribe'  => '/actions/BatchView',
                                        'tabToCreate'       => 'contentWorkspaceBatch',
                                        'tabTitle'          => gettext('Batch'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.BatchView',
                                        'file'              => 'BatchView',
                                        'style'             => 'voucherTab'
                            ));

             //Users Manage Grid
             array_push($struct, array( 'eventToSubscribe'  => '/actions/UsersManage',
                                        'tabToCreate'       => 'contentWorkspaceUsers',
                                        'tabTitle'          => gettext('Users'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Users',
                                        'file'              => 'Users',
                                        'style'             => 'userTab'
                            ));

            //User View
             array_push($struct, array( 'eventToSubscribe'  => '/actions/UserView',
                                        'tabToCreate'       => 'contentWorkspaceUser',
                                        'tabTitle'          => gettext('User'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.UserView',
                                        'file'              => 'UserView',
                                        'style'             => 'userTab'
                            ));
            //Credit View
             array_push($struct, array( 'eventToSubscribe'  => '/actions/Credits',
                                        'tabToCreate'       => 'contentWorkspaceCredits',
                                        'tabTitle'          => gettext('Internet Credit'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Credits',
                                        'file'              => 'Credits',
                                        'style'             => 'creditTab'
                            ));



            //Realms Manage
            array_push($struct, array( 'eventToSubscribe'  => '/actions/RealmsManage',
                                        'tabToCreate'       => 'contentWorkspaceRealms',
                                        'tabTitle'          => gettext('Realms'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Realms',
                                        'file'              => 'Realms',
                                        'style'             => 'realmTab'
                            ));
            //Realm View
            array_push($struct, array( 'eventToSubscribe'  => '/actions/RealmView',
                                        'tabToCreate'       => 'contentWorkspaceRealmView',
                                        'tabTitle'          => gettext('Realm'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.RealmView',
                                        'file'              => 'RealmView',
                                        'style'             => 'realmTab'
                            ));

            //Nas Manage
            array_push($struct, array( 'eventToSubscribe'  => '/actions/NasManage',
                                        'tabToCreate'       => 'contentWorkspaceNas',
                                        'tabTitle'          => gettext('Nas Devices'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Nas',
                                        'file'              => 'Nas',
                                        'style'             => 'realmTab'
                            ));

            //Nas View
            array_push($struct, array( 'eventToSubscribe'  => '/actions/NasView',
                                        'tabToCreate'       => 'contentWorkspaceNasView',
                                        'tabTitle'          => gettext('Nas Device'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.NasView',
                                        'file'              => 'NasView',
                                        'style'             => 'realmTab'
                            ));



            //Ap Manage
            array_push($struct, array(  'eventToSubscribe'  => '/actions/APManage',
                                        'tabToCreate'       => 'contentWorkspaceAccessProviders',
                                        'tabTitle'          => gettext('Access Providers'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.AccessProviders',
                                        'file'              => 'AccessProviders',
                                        'style'             => 'realmTab'
                            ));

             //Ap View
            array_push($struct, array(  'eventToSubscribe'  => '/actions/APView',
                                        'tabToCreate'       => 'contentWorkspaceAPView',
                                        'tabTitle'          => gettext('Access Provider'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.APView',
                                        'file'              => 'APView',
                                        'style'             => 'realmTab'
                            ));

             //Auto Setup
            array_push($struct, array(  'eventToSubscribe'  => '/actions/AutoSetup',
                                        'tabToCreate'       => 'contentWorkspaceAutoSetup',
                                        'tabTitle'          => gettext('Auto Setup'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.AutoSetup',
                                        'file'              => 'AutoSetup',
                                        'style'             => 'realmTab'
                            ));

            //Auto Setup View
            array_push($struct, array( 'eventToSubscribe'  => '/actions/ASView',
                                        'tabToCreate'       => 'contentWorkspaceASView',
                                        'tabTitle'          => gettext('Auto Setup'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.ASView',
                                        'file'              => 'ASView',
                                        'style'             => 'realmTab'
                            ));

            //Maps
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Maps',
                                        'tabToCreate'       => 'contentWorkspaceMaps',
                                        'tabTitle'          => gettext('Google Maps'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Maps',
                                        'file'              => 'Maps',
                                        'style'             => 'realmTab'
                            ));


            //Profile Templates Manage
            array_push($struct, array( 'eventToSubscribe'  => '/actions/Templates',
                                        'tabToCreate'       => 'contentWorkspaceTemplates',
                                        'tabTitle'          => gettext('Profile Templates'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Templates',
                                        'file'              => 'Templates',
                                        'style'             => 'profileTab'
                            ));

            //Profile Template View
            array_push($struct, array( 'eventToSubscribe'  => '/actions/TemplateView',
                                        'tabToCreate'       => 'contentWorkspaceTemplateView',
                                        'tabTitle'          => gettext('Template'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.TemplateView',
                                        'file'              => 'TemplateView',
                                        'style'             => 'profileTab'
                            ));

            //Profiles Manage
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Profiles',
                                        'tabToCreate'       => 'contentWorkspaceProfiles',
                                        'tabTitle'          => gettext('Profiles'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Profiles',
                                        'file'              => 'Profiles',
                                        'style'             => 'profileTab'
                            ));

            //Profile View
            array_push($struct, array(  'eventToSubscribe'  => '/actions/ProfileView',
                                        'tabToCreate'       => 'contentWorkspaceProfileView',
                                        'tabTitle'          => gettext('Profile'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.ProfileView',
                                        'file'              => 'ProfileView',
                                        'style'             => 'profileTab'
                            ));

             //Activity
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Activity',
                                        'tabToCreate'       => 'contentWorkspaceActivity',
                                        'tabTitle'          => gettext('Activity'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.Activity',
                                        'file'              => 'Activity',
                                        'style'             => 'statsTab'
                            ));

            //Accounts
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Accounts',
                                        'tabToCreate'       => 'contentWorkspaceAccounts',
                                        'tabTitle'          => gettext('Accounts'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.Accounts',
                                        'file'              => 'Accounts',
                                        'style'             => 'accountTab'
                            ));

            //Account View
            array_push($struct, array(  'eventToSubscribe'  => '/actions/AccountView',
                                        'tabToCreate'       => 'contentWorkspaceAccountView',
                                        'tabTitle'          => gettext('Account'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.AccountView',
                                        'file'              => 'AccountView',
                                        'style'             => 'accountTab'
                            ));

            //Billing Plans
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Plans',
                                        'tabToCreate'       => 'contentWorkspacePlans',
                                        'tabTitle'          => gettext('Billing Plans'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.Plans',
                                        'file'              => 'Plans',
                                        'style'             => 'accountTab'
                            ));

            //Billing Plan View
            array_push($struct, array(  'eventToSubscribe'  => '/actions/PlanView',
                                        'tabToCreate'       => 'contentWorkspacePlanView',
                                        'tabTitle'          => gettext('Plan'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.PlanView',
                                        'file'              => 'PlanView',
                                        'style'             => 'accountTab'
                            ));

            //Credit Card Transactions
            array_push($struct, array(  'eventToSubscribe'  => '/actions/CreditCard',
                                        'tabToCreate'       => 'contentWorkspaceCreditCard',
                                        'tabTitle'          => gettext('Credit Card Transactions'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.CreditCard',
                                        'file'              => 'CreditCard',
                                        'style'             => 'accountTab'
                            ));

            //Credit Card Transaction view
            array_push($struct, array(  'eventToSubscribe'  => '/actions/CreditCardView',
                                        'tabToCreate'       => 'contentWorkspaceCreditCardView',
                                        'tabTitle'          => gettext('Credit Card Transaction'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.CreditCardView',
                                        'file'              => 'CreditCardView',
                                        'style'             => 'accountTab'
                            ));


            //Firewall
            /*
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Firewall',
                                        'tabToCreate'       => 'contentWorkspaceFirewall',
                                        'tabTitle'          => gettext('Dynamic Firewall'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.Firewall',
                                        'file'              => 'Firewall'
                            ));
            */

             //Access Points
            array_push($struct, array(  'eventToSubscribe'  => '/actions/AccessPoints',
                                        'tabToCreate'       => 'contentWorkspaceAccessPoints',
                                        'tabTitle'          => 'Access Points',
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.AccessPoints',
                                        'file'              => 'AccessPoints',
                                        'style'             => 'statsTab'
                            ));

            //Access Point View
            array_push($struct, array(  'eventToSubscribe'  => '/actions/AccessPointView',
                                        'tabToCreate'       => 'contentWorkspaceAccessPointView',
                                        'tabTitle'          => 'Access Point',
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.AccessPointView',
                                        'file'              => 'AccessPointView',
                                        'style'             => 'statsTab'
                            ));

            //NAS Usage
            array_push($struct, array(  'eventToSubscribe'  => '/actions/StatsNas',
                                        'tabToCreate'       => 'contentWorkspaceStatsNas',
                                        'tabTitle'          => gettext('NAS Usage'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.StatsNas',
                                        'file'              => 'StatsNas',
                                        'style'             => 'statsTab'
                            ));

            //Realm Usage
            array_push($struct, array(  'eventToSubscribe'  => '/actions/StatsRealm',
                                        'tabToCreate'       => 'contentWorkspaceStatsRealm',
                                        'tabTitle'          => gettext('Realm Usage'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.StatsRealm',
                                        'file'              => 'StatsRealm',
                                        'style'             => 'statsTab'

                            ));

            //User Usage
            array_push($struct, array(  'eventToSubscribe'  => '/actions/StatsUser',
                                        'tabToCreate'       => 'contentWorkspaceStatsUser',
                                        'tabTitle'          => gettext('User Usage'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.StatsUser',
                                        'file'              => 'StatsUser',
                                        'style'             => 'statsTab'
                            ));


            return $struct;
    }

    function _ap_structure(){

        $struct = array();
         //Home tab
             array_push($struct, array( 'eventToSubscribe'  => '/actions/Homepage',
                                        'tabToCreate'       => 'contentWorkspaceHome',
                                        'tabTitle'          => gettext('Home'),
                                        'closable'          =>  false,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Homepage',
                                        'file'              => 'Homepage',
                                        'style'             => 'homeTab'
                            ));

       
            //Vouchers Manage Grid
             array_push($struct, array( 'eventToSubscribe'  => '/actions/VouchersManage',
                                        'tabToCreate'       => 'contentWorkspaceVouchers',
                                        'tabTitle'          => gettext('Vouchers'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Vouchers',
                                        'file'              => 'Vouchers',
                                        'style'             => 'voucherTab'
                            ));

            //Voucher View
             array_push($struct, array( 'eventToSubscribe'  => '/actions/VoucherView',
                                        'tabToCreate'       => 'contentWorkspaceVoucher',
                                        'tabTitle'          => gettext('Voucher'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.VoucherView',
                                        'file'              => 'VoucherView',
                                        'style'             => 'voucherTab'
                            ));

            //Voucher Batches Manage Grid
            array_push($struct, array(  'eventToSubscribe'  => '/actions/BatchesManage',
                                        'tabToCreate'       => 'contentWorkspaceBatches',
                                        'tabTitle'          => gettext('Batches'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Batches',
                                        'file'              => 'Batches',
                                        'style'             => 'voucherTab'
                            ));

            //Voucher Batches View Batch
            array_push($struct, array(  'eventToSubscribe'  => '/actions/BatchView',
                                        'tabToCreate'       => 'contentWorkspaceBatch',
                                        'tabTitle'          => gettext('Batch'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.BatchView',
                                        'file'              => 'BatchView',
                                        'style'             => 'voucherTab'
                            ));

            //Users Manage Grid
             array_push($struct, array( 'eventToSubscribe'  => '/actions/UsersManage',
                                        'tabToCreate'       => 'contentWorkspaceUsers',
                                        'tabTitle'          => gettext('Users'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Users',
                                        'file'              => 'Users',
                                        'style'             => 'userTab'
                            ));

             //User View
             array_push($struct, array( 'eventToSubscribe'  => '/actions/UserView',
                                        'tabToCreate'       => 'contentWorkspaceUser',
                                        'tabTitle'          => gettext('User'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.UserView',
                                        'file'              => 'UserView',
                                        'style'             => 'userTab'
                            ));

            //Credit View
             array_push($struct, array( 'eventToSubscribe'  => '/actions/Credits',
                                        'tabToCreate'       => 'contentWorkspaceCredits',
                                        'tabTitle'          => gettext('Internet Credit'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Credits',
                                        'file'              => 'Credits',
                                        'style'             => 'creditTab'
                            ));

            //Nas Manage
            array_push($struct, array( 'eventToSubscribe'  => '/actions/NasManage',
                                        'tabToCreate'       => 'contentWorkspaceNas',
                                        'tabTitle'          => gettext('Nas Devices'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Nas',
                                        'file'              => 'Nas',
                                        'style'             => 'realmTab'
                            ));

            //Nas View
            array_push($struct, array( 'eventToSubscribe'  => '/actions/NasView',
                                        'tabToCreate'       => 'contentWorkspaceNasView',
                                        'tabTitle'          => gettext('Nas Device'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.NasView',
                                        'file'              => 'NasView',
                                        'style'             => 'realmTab'
                            ));

            //Maps
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Maps',
                                        'tabToCreate'       => 'contentWorkspaceMaps',
                                        'tabTitle'          => gettext('Google Maps'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Maps',
                                        'file'              => 'Maps',
                                        'style'             => 'realmTab'
                            ));


            //Profile Templates Manage
            array_push($struct, array( 'eventToSubscribe'  => '/actions/Templates',
                                        'tabToCreate'       => 'contentWorkspaceTemplates',
                                        'tabTitle'          => gettext('Profile Templates'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Templates',
                                        'file'              => 'Templates',
                                        'style'             => 'profileTab'
                            ));

            //Profile Template View
            array_push($struct, array( 'eventToSubscribe'  => '/actions/TemplateView',
                                        'tabToCreate'       => 'contentWorkspaceTemplateView',
                                        'tabTitle'          => gettext('Template'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.TemplateView',
                                        'file'              => 'TemplateView',
                                        'style'             => 'profileTab'
                            ));

            //Profiles Manage
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Profiles',
                                        'tabToCreate'       => 'contentWorkspaceProfiles',
                                        'tabTitle'          => gettext('Profiles'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Profiles',
                                        'file'              => 'Profiles',
                                        'style'             => 'profileTab'
                            ));

            //Profile View
            array_push($struct, array(  'eventToSubscribe'  => '/actions/ProfileView',
                                        'tabToCreate'       => 'contentWorkspaceProfileView',
                                        'tabTitle'          => gettext('Profile'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.ProfileView',
                                        'file'              => 'ProfileView',
                                        'style'             => 'profileTab'
                            ));
             //Activity
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Activity',
                                        'tabToCreate'       => 'contentWorkspaceActivity',
                                        'tabTitle'          => gettext('Activity'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.Activity',
                                        'file'              => 'Activity',
                                        'style'             => 'statsTab'
                            ));


            //Accounting Part
            if($this->Dojolayout->_look_for_right('accnts/json_index')){
             //Accounts
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Accounts',
                                        'tabToCreate'       => 'contentWorkspaceAccounts',
                                        'tabTitle'          => gettext('Accounts'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.Accounts',
                                        'file'              => 'Accounts',
                                        'style'             => 'accountTab'
                            ));

            //Account View
            array_push($struct, array(  'eventToSubscribe'  => '/actions/AccountView',
                                        'tabToCreate'       => 'contentWorkspaceAccountView',
                                        'tabTitle'          => gettext('Account'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.AccountView',
                                        'file'              => 'AccountView',
                                        'style'             => 'accountTab'
                            ));

            //Billing Plans
            array_push($struct, array(  'eventToSubscribe'  => '/actions/Plans',
                                        'tabToCreate'       => 'contentWorkspacePlans',
                                        'tabTitle'          => gettext('Billing Plans'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.Plans',
                                        'file'              => 'Plans',
                                        'style'             => 'accountTab'
                            ));

            //Billing Plan View
            array_push($struct, array(  'eventToSubscribe'  => '/actions/PlanView',
                                        'tabToCreate'       => 'contentWorkspacePlanView',
                                        'tabTitle'          => gettext('Plan'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.PlanView',
                                        'file'              => 'PlanView',
                                        'style'             => 'accountTab'
                            ));
            }

             //NAS Usage
            array_push($struct, array(  'eventToSubscribe'  => '/actions/StatsNas',
                                        'tabToCreate'       => 'contentWorkspaceStatsNas',
                                        'tabTitle'          => gettext('NAS Usage'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.StatsNas',
                                        'file'              => 'StatsNas',
                                        'style'             => 'statsTab'
                            ));

            //Realm Usage
            array_push($struct, array(  'eventToSubscribe'  => '/actions/StatsRealm',
                                        'tabToCreate'       => 'contentWorkspaceStatsRealm',
                                        'tabTitle'          => gettext('Realm Usage'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.StatsRealm',
                                        'file'              => 'StatsRealm',
                                        'style'             => 'statsTab'

                            ));

            //User Usage
            array_push($struct, array(  'eventToSubscribe'  => '/actions/StatsUser',
                                        'tabToCreate'       => 'contentWorkspaceStatsUser',
                                        'tabTitle'          => gettext('User Usage'),
                                        'closable'          =>  true,
                                        'includeId'         =>  true,
                                        'module'            => 'content.StatsUser',
                                        'file'              => 'StatsUser',
                                        'style'             => 'statsTab'
                            ));


            return $struct;

    }

     function _user_structure(){

        $struct = array();
         //Home tab
             array_push($struct, array( 'eventToSubscribe'  => '/actions/Homepage',
                                        'tabToCreate'       => 'contentWorkspaceHome',
                                        'tabTitle'          => gettext('Home'),
                                        'closable'          =>  false,
                                        'includeId'         =>  false,
                                        'module'            => 'content.Permanent',
                                        'file'              => 'Permanent',
                                        'style'             => 'homeTab'
                            ));
             array_push($struct, array( 'eventToSubscribe'  => '/actions/PermanentGeneral',
                                        'tabToCreate'       => 'contentPermanentGeneral',
                                        'tabTitle'          => gettext('General Detail'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.PermanentGeneral',
                                        'file'              => 'PermanentGeneral',
                                        'style'             => 'userTab'
                            ));

             array_push($struct, array( 'eventToSubscribe'  => '/actions/PermanentInvoices',
                                        'tabToCreate'       => 'contentPermanentInvoices',
                                        'tabTitle'          => gettext('Invoices'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.PermanentInvoices',
                                        'file'              => 'PermanentInvoices',
                                        'style'             => 'accountTab'
                            ));

              array_push($struct, array( 'eventToSubscribe'  => '/actions/PermanentPayments',
                                        'tabToCreate'       => 'contentPermanentPayments',
                                        'tabTitle'          => gettext('Payments'),
                                        'closable'          =>  true,
                                        'includeId'         =>  false,
                                        'module'            => 'content.PermanentPayments',
                                        'file'              => 'PermanentPayments',
                                        'style'             => 'accountTab'
                            ));

        //Maps
        array_push($struct, array(  'eventToSubscribe'  => '/actions/Maps',
                                     'tabToCreate'      => 'contentWorkspaceMaps',
                                     'tabTitle'         => gettext('Google Maps'),
                                     'closable'         =>  true,
                                     'includeId'        =>  false,
                                     'module'           => 'content.Maps',
                                     'file'             => 'Maps',
                                     'style'            => 'realmTab'
        ));

            return $struct;
    }
}
?>
