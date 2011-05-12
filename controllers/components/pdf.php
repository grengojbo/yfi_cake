<?php

class PdfComponent extends Object {

    var $components = array('Formatter'); 

   function latest_invoices($pdf_structure){

        //print_r($pdf_structure);
        //return;

        App::import('Vendor', 'fpdf/invoice');
        $pdf_invoice = new PDF_Invoice();
        $pdf_invoice->AliasNbPages();
        $pdf_invoice->Title = gettext('Invoice');

         //------------------------------------
         foreach($pdf_structure as $invoice){

            $pdf_invoice->Logo = 'img/graphics/'.$invoice['Realm']['icon_file_name'];

            $pdf_invoice->AddPage();
            $pdf_invoice->addAccessProvider($invoice['Realm']);         //Add the Realm

            //Reverse the invoice list so newest is first
            $invoice['Invoice'] = array_reverse($invoice['Invoice']);
            $invoice_id         = $invoice['Invoice'][0]['id'];
            $pdf_invoice->addInvoiceNumber($invoice_id);                //Add the invoice number
            $pdf_invoice->addCustomerDetail($invoice['User']);          //Add custome detail
            
            //-----------Billing Plan detail---------------------
            $plan_detail = array();
            $plan_detail['start_date']  = $invoice['Invoice'][0]['start_date'];
            $plan_detail['end_date']    = $invoice['Invoice'][0]['end_date'];
            $plan_detail['name']        = $invoice['Invoice'][0]['BillingPlan']['name'];
            $pdf_invoice->addBillingDetail($plan_detail);


            //-----------Usage Totals----------------------------
            $usage_totals           = array();
            $usage_std_data         = $invoice['Invoice'][0]['Usage']['cap_data'];
            $usage_std_time         = $invoice['Invoice'][0]['Usage']['cap_time'];
            $usage_extra_data       = $invoice['Invoice'][0]['Usage']['extra_data'];
            $usage_extra_time       = $invoice['Invoice'][0]['Usage']['extra_time'];
            $free_data              = $invoice['Invoice'][0]['BillingPlan']['free_data'];
            $free_time              = $invoice['Invoice'][0]['BillingPlan']['free_time'];


            $std_time_formatted     = $this->Formatter->_sec2hms($usage_std_time);
            $free_time_formatted    = $this->Formatter->_sec2hms($free_time);
            $extra_time_formatted   = $this->Formatter->_sec2hms($usage_extra_time);

            array_push($usage_totals,array(gettext('Standard Data'),     $this->Formatter->formatted_bytes($usage_std_data)));
            array_push($usage_totals,array(gettext('Free Data'),         $this->Formatter->formatted_bytes($free_data)));
            array_push($usage_totals,array(gettext('Extra Data'),        $this->Formatter->formatted_bytes($usage_extra_data)));
            array_push($usage_totals,array(gettext('Standard Time'),     $std_time_formatted));
            array_push($usage_totals,array(gettext('Free Time'),         $free_time_formatted));
            array_push($usage_totals,array(gettext('Extra Time'),        $extra_time_formatted));

            $y_start = $pdf_invoice->GetY();
            $pdf_invoice->addUsageTotals($y_start,$usage_totals);


            //-----------Stats Item-----------------------------
            $stats              = array();
            $stats_invoice_sum  = $invoice['Invoice'][0]['Stats']['invoice_sum'];
            $stats_payment_sum  = $invoice['Invoice'][0]['Stats']['payment_sum'];
            $stats_outstanding  = $invoice['Invoice'][0]['Stats']['outstanding'];

            array_push($stats,array(gettext('Total Invoiced'),   $stats_invoice_sum));
            array_push($stats,array(gettext('Total Paid'),       $stats_payment_sum));
            array_push($stats,array(gettext('Outstanding'),      $stats_outstanding));

            $y_end = $pdf_invoice->GetY();
            $pdf_invoice->addStats($y_start, $stats);

            //--------Cost Breakdown--------------------------

            //Cost breakdown
            $cost = array();
            $std_data_total     = $usage_std_data - $free_data;
            ($std_data_total <= 0)&&($std_data_total = 0);
            $std_time_total     = $usage_std_time - $free_time;
            ($std_time_total <= 0)&&($std_time_total = 0);

            $subscription       = $invoice['Invoice'][0]['BillingPlan']['subscription'];
            $discount           = $invoice['Invoice'][0]['BillingPlan']['discount'];
            $tax                = $invoice['Invoice'][0]['BillingPlan']['tax'];
            $currency           = $invoice['Invoice'][0]['BillingPlan']['currency'];
            $data_unit          = $invoice['Invoice'][0]['BillingPlan']['data_unit'];
            $time_unit          = $invoice['Invoice'][0]['BillingPlan']['time_unit'];
            $extra_data         = $invoice['Invoice'][0]['Usage']['extra_data'];
            $extra_time         = $invoice['Invoice'][0]['Usage']['extra_time'];

            $pay_cap_data       = $invoice['Invoice'][0]['Payable']['cap_data'];
            $pay_extra_data     = $invoice['Invoice'][0]['Payable']['extra_data'];
            $pay_cap_time       = $invoice['Invoice'][0]['Payable']['cap_time'];
            $pay_extra_time     = $invoice['Invoice'][0]['Payable']['extra_time'];
            $pay_before_disc    = $invoice['Invoice'][0]['Payable']['before_discount'];
            $pay_after_disc     = $invoice['Invoice'][0]['Payable']['after_discount'];
            $discount_total     = round($pay_before_disc - $pay_after_disc,2);
            $extra_services     = $invoice['Invoice'][0]['ExtraService']['Total'];
            $before_tax         = $invoice['Invoice'][0]['Payable']['total_before_tax'];
            $with_tax           = $invoice['Invoice'][0]['Payable']['total_with_tax'];

            array_push($cost,array(gettext('Subscription'),      $subscription,      '1',                $subscription));
            array_push($cost,array(gettext('Normal Cost/Byte'),  $data_unit,         $std_data_total,    $pay_cap_data));
            array_push($cost,array(gettext('Extra Cost/Byte'),   $data_unit.' x '.$extra_data, $usage_extra_data,$pay_extra_data));
            array_push($cost,array(gettext('Normal Cost/Second'),$time_unit,        $std_time_total,     $pay_cap_time));
            array_push($cost,array(gettext('Extra Cost/Second'), $time_unit.' x '.$extra_time,$extra_time,$pay_extra_time));
            array_push($cost,array(gettext('Total Usage Discount'),$discount.' %',   $pay_before_disc,   $discount_total));
            array_push($cost,array(gettext('Extra Services'),    $extra_services,    '1',                $extra_services));
            array_push($cost,array(gettext('Tax'),               $tax.' %',          $before_tax,        $with_tax - $before_tax));
            $outstanding = round($stats_outstanding - $with_tax,2);
            array_push($cost,array(gettext('Prevoius Outstanding'),       $outstanding,       1,                 $outstanding));


            if(count($invoice['Invoice'][0]['ExtraService']['Items'])>=1){
                //Extra Services Data
                $extra_services = array();
                foreach($invoice['Invoice'][0]['ExtraService']['Items'] as $service){
                    array_push($extra_services,array($service['title'],$service['description'],$service['amount']));
                }
                $pdf_invoice->addExtraServices($y_end,$extra_services);
                $pdf_invoice->addCostBreakdown($pdf_invoice->GetY(),$cost,$stats_outstanding,$currency);
            }else{
                $pdf_invoice->addCostBreakdown($y_end,$cost,$stats_outstanding,$currency);
            }
        }
        return $pdf_invoice;
    }

    function selected_invoices_for_user($invoices_for_user,$s_i){

        App::import('Vendor', 'fpdf/invoice');
        $pdf_invoice = new PDF_Invoice();
        $pdf_invoice->AliasNbPages();
        $pdf_invoice->Title = gettext('Invoice');

        //------------------------------------
         foreach($invoices_for_user['Invoice'] as $invoice){

            //Should we do this one?
            $invoice_id         = $invoice['id'];
            if(!(in_array($invoice_id,$s_i))){
                continue;
            }

            $pdf_invoice->Logo = 'img/graphics/'.$invoices_for_user['Realm']['icon_file_name'];
            $pdf_invoice->AddPage();
            $pdf_invoice->addAccessProvider($invoices_for_user['Realm']);         //Add the Realm
            $pdf_invoice->addInvoiceNumber($invoice_id);                //Add the invoice number
            $pdf_invoice->addCustomerDetail($invoices_for_user['User']);          //Add custome detail

            //-----------Billing Plan detail---------------------
            $plan_detail = array();
            $plan_detail['start_date']  = $invoice['start_date'];
            $plan_detail['end_date']    = $invoice['end_date'];
            $plan_detail['name']        = $invoice['BillingPlan']['name'];
            $pdf_invoice->addBillingDetail($plan_detail);


            //-----------Usage Totals----------------------------
            $usage_totals           = array();
            $usage_std_data         = $invoice['Usage']['cap_data'];
            $usage_std_time         = $invoice['Usage']['cap_time'];
            $usage_extra_data       = $invoice['Usage']['extra_data'];
            $usage_extra_time       = $invoice['Usage']['extra_time'];
            $free_data              = $invoice['BillingPlan']['free_data'];
            $free_time              = $invoice['BillingPlan']['free_time'];

            $std_time_formatted     = $this->Formatter->_sec2hms($usage_std_time);
            $free_time_formatted    = $this->Formatter->_sec2hms($free_time);
            $extra_time_formatted   = $this->Formatter->_sec2hms($usage_extra_time);

            array_push($usage_totals,array(gettext('Standard Data'),     $this->Formatter->formatted_bytes($usage_std_data)));
            array_push($usage_totals,array(gettext('Free Data'),         $this->Formatter->formatted_bytes($free_data)));
            array_push($usage_totals,array(gettext('Extra Data'),        $this->Formatter->formatted_bytes($usage_extra_data)));
            array_push($usage_totals,array(gettext('Standard Time'),     $std_time_formatted));
            array_push($usage_totals,array(gettext('Free Time'),         $free_time_formatted));
            array_push($usage_totals,array(gettext('Extra Time'),        $extra_time_formatted));

            $y_start = $pdf_invoice->GetY();
            $pdf_invoice->addUsageTotals($y_start,$usage_totals);

            //-----------Stats Item-----------------------------
            $stats              = array();
            $stats_invoice_sum  = $invoice['Stats']['invoice_sum'];
            $stats_payment_sum  = $invoice['Stats']['payment_sum'];
            $stats_outstanding  = $invoice['Stats']['outstanding'];

            array_push($stats,array(gettext('Total Invoiced'),   $stats_invoice_sum));
            array_push($stats,array(gettext('Total Paid'),       $stats_payment_sum));
            array_push($stats,array(gettext('Outstanding'),      $stats_outstanding));

            $y_end = $pdf_invoice->GetY();
            $pdf_invoice->addStats($y_start, $stats);

            //--------Cost Breakdown--------------------------

            //Cost breakdown
            $cost = array();
            $std_data_total     = $usage_std_data - $free_data;
            ($std_data_total <= 0)&&($std_data_total = 0);
            $std_time_total     = $usage_std_time - $free_time;
            ($std_time_total <= 0)&&($std_time_total = 0);

            $subscription       = $invoice['BillingPlan']['subscription'];
            $discount           = $invoice['BillingPlan']['discount'];
            $tax                = $invoice['BillingPlan']['tax'];
            $currency           = $invoice['BillingPlan']['currency'];
            $data_unit          = $invoice['BillingPlan']['data_unit'];
            $time_unit          = $invoice['BillingPlan']['time_unit'];
            $extra_data         = $invoice['Usage']['extra_data'];
            $extra_time         = $invoice['Usage']['extra_time'];

            $pay_cap_data       = $invoice['Payable']['cap_data'];
            $pay_extra_data     = $invoice['Payable']['extra_data'];
            $pay_cap_time       = $invoice['Payable']['cap_time'];
            $pay_extra_time     = $invoice['Payable']['extra_time'];
            $pay_before_disc    = $invoice['Payable']['before_discount'];
            $pay_after_disc     = $invoice['Payable']['after_discount'];
            $discount_total     = round($pay_before_disc - $pay_after_disc,2);
            $extra_services     = $invoice['ExtraService']['Total'];
            $before_tax         = $invoice['Payable']['total_before_tax'];
            $with_tax           = $invoice['Payable']['total_with_tax'];

            array_push($cost,array(gettext('Subscription'),      $subscription,      '1',                $subscription));
            array_push($cost,array(gettext('Normal Cost/Byte'),  $data_unit,         $std_data_total,    $pay_cap_data));
            array_push($cost,array(gettext('Extra Cost/Byte'),   $data_unit.' x '.$extra_data, $usage_extra_data,$pay_extra_data));
            array_push($cost,array(gettext('Normal Cost/Second'),$time_unit,        $std_time_total,     $pay_cap_time));
            array_push($cost,array(gettext('Extra Cost/Second'), $time_unit.' x '.$extra_time,$extra_time,$pay_extra_time));
            array_push($cost,array(gettext('Total Usage Discount'),$discount.' %',   $pay_before_disc,   $discount_total));
            array_push($cost,array(gettext('Extra Services'),    $extra_services,    '1',                $extra_services));
            array_push($cost,array(gettext('Tax'),               $tax.' %',          $before_tax,        $with_tax - $before_tax));
            $outstanding = round($stats_outstanding - $with_tax,2);
            array_push($cost,array(gettext('Previous Outstanding'),       $outstanding,       1,                 $outstanding));
            
            //____ Extra Services or NOT? ____
            if(count($invoice['ExtraService']['Items'])>=1){
                //Extra Services Data
                $extra_services = array();
                foreach($invoice['ExtraService']['Items'] as $service){
                    array_push($extra_services,array($service['title'],$service['description'],$service['amount']));
                }
                $pdf_invoice->addExtraServices($y_end,$extra_services);
                $pdf_invoice->addCostBreakdown($pdf_invoice->GetY(),$cost,$stats_outstanding,$currency);
            }else{
                $pdf_invoice->addCostBreakdown($y_end,$cost,$stats_outstanding,$currency);
            }
        }
        //------------------------------------
       return $pdf_invoice;
    }

     function selected_payments_for_user($payments_for_user,$s_p){

        App::import('Vendor', 'fpdf/receipt');
        $pdf_receipt = new PDF_Receipt();
        $pdf_receipt->AliasNbPages();
        $pdf_receipt->Title = gettext('Receipt');

        //------------------------------------
         foreach($payments_for_user['Payment'] as $payment){

            //Should we do this one?
            $payment_id         = $payment['id'];
            $amount             = $payment['amount'];
            $date               = $payment['created'];
            $date               = split(" ",$date);
            if(!(in_array($payment_id,$s_p))){
                continue;
            }

            $pdf_receipt->Logo = 'img/graphics/'.$payments_for_user['Realm']['icon_file_name'];
            $pdf_receipt->AddPage();
            $pdf_receipt->addAccessProvider($payments_for_user['Realm']);         //Add the Realm
            $pdf_receipt->addReceiptNumber($payment_id);                //Add the invoice number

              //-----------Payment detail---------------------
            $payment_detail = array();
            $payment_detail['payment_date'] = $date[0];
            $payment_detail['amount']       = $amount;
            $pdf_receipt->addPaymentDetail($payment_detail);

            $pdf_receipt->addCustomerDetail($payments_for_user['User']);          //Add custome detail
            $pdf_receipt->addAmount($amount);                //Add the invoice number

            /*
            //-----------Stats Item-----------------------------
            $stats              = array();
            $stats_invoice_sum  = $invoice['Stats']['invoice_sum'];
            $stats_payment_sum  = $invoice['Stats']['payment_sum'];
            $stats_outstanding  = $invoice['Stats']['outstanding'];

            array_push($stats,array('Total Invoiced',   $stats_invoice_sum));
            array_push($stats,array('Total Paid',       $stats_payment_sum));
            array_push($stats,array('Outstanding',      $stats_outstanding));

            $y_end = $pdf_invoice->GetY();
            $pdf_receipt->addStats($y_start, $stats);
            */
        }
        //------------------------------------
       return $pdf_receipt;
    }


}

?>