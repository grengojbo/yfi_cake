<?php
    Configure::write('debug', 0);
  //  _build_invoices($pdf_structure);
    return;

    function _build_invoices($pdf_structure){

      //  App::import('Vendor', 'fpdf/invoice');
      //  $pdf_invoice = new PDF_Generic();
      //  $pdf_invoice->AliasNbPages();
      //  $pdf_invoice->Title = 'Invoice';

        print_r($pdf_structure);
        return;

        //------------------------------------
         foreach(array_keys($pdf_structure) as $invoice){

            print_r($invoice);
        }
        //------------------------------------

       // $pdf_invoice->Output();
    }


?>