<?php
class BookkeeperComponent extends Object {

    //---------------------------------------------------------
    //----The Bookkeeper Component ----------------------------
    //---------------------------------------------------------

    /*This component is used to do various calculations on the usage
        of permanent users and how much they need to pay etc
        The controller which calls it needs to use the following models
        var $uses       = array('BillingPlan','BillingPlanRealm','Invoice','User','Radacct');
    */

   //This component will determine what will be displayed when logged in - determined by rights
    var $components = array('Session','Rights'); 


    function initialize(&$controller) {
        // saving the controller reference for later use
        $this->controller =& $controller;
    }

    function invoice_detail_for($user_id) {

        //This will return a data structure with invoice detail for all invoices issued to the specified user
        $userdetail = $this->controller->User->findById($user_id); //This return value will also show all the invoices tied to user
        $username   = $userdetail['User']['username'];
        $profile    = $userdetail['Profile']['name'];

        $counter =0;
        foreach($userdetail['Invoice'] as $item){

                $bp_detail      = $this->controller->BillingPlan->findById($item['billing_plan_id']);
                $userdetail['Invoice'][$counter]['BillingPlan'] = $bp_detail['BillingPlan'];
                //Calculate the totals for this billing plan
                $usage_totals       = $this->_invoice_usage_totals($username,$profile,$item); //Get the total usage - and how much of it is extra cap
                $userdetail['Invoice'][$counter]['Usage']   = $usage_totals;
                $extra_services     = $this->_extra_services($user_id,$item);
                $userdetail['Invoice'][$counter]['ExtraService'] = $extra_services;
                $userdetail['Invoice'][$counter]['Payable'] = $this->_invoice_pay_totals($bp_detail['BillingPlan'],$usage_totals,$extra_services['Total']);
                $counter++;
        }

        //____Create a Stats part to each invoice indicating outstanding / paid until today___
        $counter                = 0;
        $sum_of_invoices        = 0;
        //First we need to reverse the array
        $userdetail['Invoice']  = array_reverse($userdetail['Invoice']);
        $invoice_count          = count($userdetail['Invoice']);

        foreach($userdetail['Invoice'] as $item){
            $sum_of_invoices = $sum_of_invoices + $item['Payable']['total_with_tax'];
            //Get the payments up until this invoice was generated
            //The last invoice (first item) should include mayments up to today!
            $payments_up_to_date = $item['created'];
            ($counter == ($invoice_count-1))&&($payments_up_to_date = date('Y-m-d H:i:s', time()));
           // print_r($payments_up_to_date);
            $sum_of_payments = $this->get_payments_up_to($user_id,$payments_up_to_date);
            $userdetail['Invoice'][$counter]['Stats']['invoice_sum'] = $sum_of_invoices;
            $userdetail['Invoice'][$counter]['Stats']['payment_sum'] = $sum_of_payments;
            $userdetail['Invoice'][$counter]['Stats']['outstanding'] = ($sum_of_invoices - $sum_of_payments);

            $counter++;
        }
        return $userdetail;
    }


    function _extra_services($user_id,$item){

        $services_total     = 0;
        $feedback           = array();
        $feedback['Items']  = array();
        $qr = $this->controller->ExtraService->find('all',array('conditions' =>array(
                                                             'ExtraService.user_id'     => $user_id,
                                                             'ExtraService.created >='  => $item['start_date'],
                                                             'ExtraService.created <='  => $item['end_date'],
                                                                        )
                                                            )
                );
        if($qr != ''){
            foreach($qr as $item){
                $services_total =  $services_total +$item['ExtraService']['amount'];
                array_push($feedback['Items'],$item['ExtraService']);
            }
        }
        $feedback['Total'] = $services_total;
       // print_r($feedback);
        return $feedback;
    }

    function _invoice_usage_totals($username,$profile,$item){

        //______TOTAL USAGE__________
        //Get the total usages for the user for the specified invoice time
        $query = "SELECT SUM(acctinputoctets) AS total_input,SUM(acctoutputoctets) AS total_output, SUM(acctsessiontime) as total_time FROM radacct AS Radacct where username='".$username."' AND acctstarttime >='".$item['start_date']."' AND acctstoptime <='".$item['end_date']."'";
        $qr = $this->controller->Radacct->query($query);
        //print_r($qr);

        //Zero them first
        $total_input = $total_output = $total_time = 0;
        $total_input    = $qr[0][0]['total_input'];
        $total_output   = $qr[0][0]['total_output'];
        $total_time     = $qr[0][0]['total_time'];

        //_____ CAP SIZE ___
        $qr             = $this->controller->Radgroupcheck->findAllByGroupname($profile);
        $cap_data       = 0;
        $cap_time       = 0;
        foreach($qr as $i){
            if($i['Radgroupcheck']['attribute'] == 'Yfi-Data'){
                if(($total_input + $total_output) <= $i['Radgroupcheck']['value']){
                    $cap_data = $total_input + $total_output;
                }else{
                    $cap_data = $i['Radgroupcheck']['value'];
                }
            }
             if($i['Radgroupcheck']['attribute'] == 'Yfi-Time'){
                if(($total_input + $total_output) <= $i['Radgroupcheck']['value']){
                    $cap_time = $total_time;
                }else{
                    $cap_time = $i['Radgroupcheck']['value'];
                }
            }
        }

        //_____ Over the CAP - How much? _______
        $total_data_extra = 0;
        $total_time_extra = 0;
        ($cap_data != 0)&&($total_data_extra = ($total_input + $total_output) - $cap_data);
        ($cap_time != 0)&&($total_time_extra = $total_time - $cap_time);
        //Zero the value of extra's of negative (Meaning the person used less than their CAP)
        ($total_time_extra < 0)&&($total_time_extra = 0);
        ($total_data_extra < 0)&&($total_data_extra = 0);

        //----------------------------------------------
        $ret_arr = array();
        $ret_arr['total_data_usage']    = $total_input + $total_output;
        $ret_arr['total_time_usage']    = $total_time;
        $ret_arr['cap_data']            = $cap_data;
        $ret_arr['cap_time']            = $cap_time;
        $ret_arr['extra_data']          = $total_data_extra;
        $ret_arr['extra_time']          = $total_time_extra;
        //print_r($ret_arr);
        return $ret_arr; 
        //---------------------------------------------
    }

    function _invoice_pay_totals($billing_plan,$usage_totals,$extra_services){

        //____ Calculate the totals for our user ___

        if($usage_totals['cap_data'] != 0){
            if(($usage_totals['total_data_usage'] - $usage_totals['cap_data']) <= 0){
                $cost_cap_data = (($usage_totals['total_data_usage'] - $billing_plan['free_data']) * $billing_plan['data_unit']);       //Usage is less than CAP
            }else{
                $cost_cap_data = (($usage_totals['cap_data'] - $billing_plan['free_data']) * $billing_plan['data_unit']);               //Usage is more than CAP
            }
        }else{ //No Cap
            $cost_cap_data = ($usage_totals['total_data_usage'] - $billing_plan['free_data']) * $billing_plan['data_unit'];
        }

        if($usage_totals['cap_time'] != 0){
            if(($usage_totals['total_data_usage'] - $usage_totals['cap_data']) <= 0){
                $cost_cap_time = (($usage_totals['total_time_usage'] - $billing_plan['free_time']) * $billing_plan['time_unit']);
            }else{
                $cost_cap_time = (($usage_totals['cap_time'] - $billing_plan['free_time']) * $billing_plan['time_unit']);
            }
        }else{ //No Cap
            $cost_cap_time = ($usage_totals['total_time_usage'] - $billing_plan['free_time']) * $billing_plan['time_unit'];
        }

        //Zero the value if the free caused a negative
        ($cost_cap_data <= 0)&&($cost_cap_data = 0);
        ($cost_cap_time <= 0)&&($cost_cap_time = 0);

        $cost_data_extra = $usage_totals['extra_data'] * $billing_plan['data_unit'] * $billing_plan['extra_data'];
        $cost_time_extra = $usage_totals['extra_time'] * $billing_plan['time_unit'] * $billing_plan['extra_time'];

        $gt_before_discount = $cost_cap_data + $cost_cap_time + $cost_time_extra + $cost_data_extra;
        $gt_after_discount  = $gt_before_discount - ($gt_before_discount *($billing_plan['discount']/100)); //Discount is only on usage
        $gt_with_tax        = ($gt_after_discount + $extra_services + $billing_plan['subscription'])* (1+($billing_plan['tax']/100));


        //--------------------------------------
        $ret_arr                = array();
        $ret_arr['cap_data']    = round($cost_cap_data, 2);
        $ret_arr['cap_time']    = round($cost_cap_time, 2);
        $ret_arr['extra_data']  = round($cost_data_extra, 2);
        $ret_arr['extra_time']  = round($cost_time_extra,2);
        $ret_arr['before_discount'] = round($gt_before_discount,2);
        $ret_arr['after_discount']  = round($gt_after_discount,2);
        $ret_arr['total_before_tax']= round($gt_after_discount+$extra_services + $billing_plan['subscription'],2);
        $ret_arr['total_with_tax']  = round($gt_with_tax,2); //includes extra services + subscription
        return $ret_arr;
        //-----------------------------------------

    }

    function get_payments_up_to($user_id,$up_to_date){
        $sum_of_payments = 0; //Zero it
        //Get the sum of payments up to the specified date for specified user
        $qr = $this->controller->Payment->find('first', array('fields' =>array("SUM(Payment.amount)as total"),'conditions' => array('Payment.user_id' => $user_id,'Payment.created <=' => $up_to_date)));
        if($qr[0]['total'] != ''){
            $sum_of_payments = $qr[0]['total'];
        }
        return $sum_of_payments;
    }

    function get_invoices_up_to($user_id,$up_to_date){

        $sum_of_invoices    = 0;
        $userdetail         = $this->controller->User->findById($user_id); //This return value will also show all the invoices tied to user
        $username           = $userdetail['User']['username'];
        $profile            = $userdetail['Profile']['name'];

        $qr = $this->controller->Invoice->find('all', array('conditions' => array('Invoice.user_id' => $user_id,'Invoice.end_date <=' => $up_to_date)));

        foreach($qr as $item){

            $bp_detail      = $this->controller->BillingPlan->findById($item['Invoice']['billing_plan_id']);
            $usage_totals   = $this->_invoice_usage_totals($username,$profile,$item['Invoice']);

            $extra_services = $this->_extra_services($user_id,$item['Invoice']);
           // print_r($item);
            $totals         = $this->_invoice_pay_totals($bp_detail['BillingPlan'],$usage_totals,$extra_services['Total']);
            $sum_of_invoices=$sum_of_invoices+$totals['total_with_tax'];
        }
     
        return $sum_of_invoices;
    }
}



?>