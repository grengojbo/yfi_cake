<?php
class DojolayoutComponent extends Object {

   //This component will determine what will be displayed when logged in - determined by rights
    var $components = array('Session','Rights','Leftpane','Workspace','Actions'); 

    function initialize(&$controller) {
        // saving the controller reference for later use
        $this->controller =& $controller;
    }

    function left_pane(){

        //Call the Leftpane Child
        return $this->Leftpane->display();
    }

    function workspace(){

        //Call Workspace Child
        return $this->Workspace->display();
    }


    function actions_for_templates(){

        return $this->Actions->actions_for_templates();
    }

    function actions_for_template_view(){

        return $this->Actions->actions_for_template_view();
    }

    function actions_for_profiles(){

        return $this->Actions->actions_for_profiles();
    }

     function actions_for_profile_view(){

        return $this->Actions->actions_for_profile_view();
    }

    function actions_for_vouchers(){

        return $this->Actions->actions_for_vouchers();
    }

    function actions_for_voucher_profile(){

        return $this->Actions->actions_for_voucher_profile();
    }

    function actions_for_voucher_private(){

        return $this->Actions->actions_for_voucher_private();
    }

    function actions_for_voucher_activity(){
        return $this->Actions->actions_for_voucher_activity();
    }

    function actions_for_batches(){

        return $this->Actions->actions_for_batches();
    }

    function actions_for_batch_view(){

        return $this->Actions->actions_for_batch_view();
    }

    function actions_for_nas(){

        return $this->Actions->actions_for_nas();
    }

    function actions_for_nas_view(){

        return $this->Actions->actions_for_nas_view();
    }

    function actions_for_activity(){

        return $this->Actions->actions_for_activity();
    }

    function actions_for_permanent_users(){
        return $this->Actions->actions_for_permanent_users();
    }

    function actions_for_user_profile(){
        return $this->Actions->actions_for_user_profile();
    }

    function actions_for_user_private(){
        return $this->Actions->actions_for_user_private();
    }
    function actions_for_user_activity(){
        return $this->Actions->actions_for_user_activity();
    }

    function actions_for_extras(){
        return $this->Actions->actions_for_extras();
    }

    function actions_for_prepaid_extras(){
        return $this->Actions->actions_for_prepaid_extras();
    }

    //-- 11-3-10 Self Service Add-on ---
    function actions_for_permanent_extras(){
       return $this->Actions->actions_for_permanent_extras();
    }
    function actions_for_permanent_prepaid_extras(){
        return $this->Actions->actions_for_permanent_prepaid_extras();
    }
    //--------- END Self Service ---------------

    function actions_for_billing_plans(){
        return $this->Actions->actions_for_billing_plans();
    }

    function actions_for_accnts(){
        return $this->Actions->actions_for_accnts();
    }

    function actions_for_accnt_invoices(){
        return $this->Actions->actions_for_accnt_invoices();
    }
     function actions_for_accnt_payments(){
        return $this->Actions->actions_for_accnt_payments();
    }

    function actions_for_cc_transactions(){
        return $this->Actions->actions_for_cc_transactions();
    }

    function actions_for_extra_services(){
        return $this->Actions->actions_for_extra_services();
    }

    function actions_for_access_points(){
        return $this->Actions->actions_for_access_points();
    }

    function actions_for_rogues(){
        return $this->Actions->actions_for_rogues();
    }

    function actions_for_clients(){
        return $this->Actions->actions_for_clients();
    }

    function actions_for_credits(){
        return $this->Actions->actions_for_credits();
    }

    function actions_for_user_credits(){
        return $this->Actions->actions_for_user_credits();
    }

	function actions_for_devices(){
        return $this->Actions->actions_for_devices();
    }

    function actions_for_maps(){
        return $this->Actions->actions_for_maps();
    }


    function _look_for_right($right){

        $auth_data = $this->Session->read('AuthInfo');

        //Admin users should have every right by default
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            return true;
        }

        if(array_key_exists('Rights',$auth_data)){
            if(array_key_exists($right,$auth_data['Rights'])){
            
                if($auth_data['Rights'][$right]['state'] == '1'){
                    return true;
                }
                return false;
            }
        }
        return false;   //Default
    }

}



?>
