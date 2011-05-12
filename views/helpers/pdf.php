<?php
/* /app/views/helpers/link.php */

class pdfHelper extends AppHelper {

    function gooi() {

        App::import('Vendor', 'fpdf/fpdf');
        $pdf=new FPDF();


        print( "Gooi hom!");
        // Logic to create specially formatted link goes here...
    }
}

?>