<?php
    Configure::write('debug', 0);

    //Depending on the format chosen we will import different classes

    if($format == 'generic'){
        _build_generic($pdf_structure,false);
        return;
    }

    if($format == 'generic_ppv'){
         _build_generic($pdf_structure,true);
        return;
    }

    //Assume an Avery label
    _build_label($format,$pdf_structure);

  //  if($format == 'label'){
  //      App::import('Vendor', 'fpdf/label');
   // }


   // $avery->test();


    function _build_generic($pdf_structure,$page_per_voucher = false){

        App::import('Vendor', 'fpdf/generic');
        $pdf_gen = new PDF_Generic();
        $pdf_gen->AliasNbPages();
        $pdf_gen->Title = gettext('Internet Access Voucher');

       // print_r($pdf_structure);

        //------------------------------------
         foreach(array_keys($pdf_structure) as $key){

            $detail = $pdf_structure[$key]['detail'];
            $pdf_gen->Logo = 'img/graphics/'.$detail['icon_file_name'];

            //For each different profile we need to add a page
            foreach(array_keys($pdf_structure[$key]['profiles']) as $profile_name){

                $vouchers = $pdf_structure[$key]['profiles'][$profile_name]['vouchers'];
                if($page_per_voucher == true){
                    foreach(array_keys($vouchers) as $voucher_name){

                        $pdf_gen->AddPage();
                        $pdf_gen->addAccessProvider($detail);
                        $profile = $pdf_structure[$key]['profiles'][$profile_name];
                        $pdf_gen->addProfileName($profile_name);
                        $profile_detail = $pdf_structure[$key]['profiles'][$profile_name]['detail'];
                        $pdf_gen->addProfileDetail($profile_detail);
                        $single_voucher = array();
                        $single_voucher[$voucher_name] = $vouchers[$voucher_name];
                        $pdf_gen->addVouchers($single_voucher);
                    }

                }else{
                    $pdf_gen->AddPage();
                    $pdf_gen->addAccessProvider($detail);
                    $profile = $pdf_structure[$key]['profiles'][$profile_name];
                    $pdf_gen->addProfileName($profile_name);
                    $profile_detail = $pdf_structure[$key]['profiles'][$profile_name]['detail'];
                    $pdf_gen->addProfileDetail($profile_detail);
                    $pdf_gen->addVouchers($vouchers);
                }
               // print_r($vouchers);

            }

          //  print_r($detail);
        }
        //------------------------------------

        $pdf_gen->Output();

    }

    function _build_label($format,$pdf_structure){

        App::import('Vendor', 'fpdf/label');
        $pdf = new PDF_Label($format);
        $pdf->AddPage();

        //--Loop through the PDF data adding labels----
       // print_r($pdf_structure);
        foreach($pdf_structure as $label){

            $pdf->Logo = 'img/graphics/'.$label['icon'];
            $pdf->Add_Label($label);

        }

        //---------------------------------------------

        $pdf->Output();
    }


?>