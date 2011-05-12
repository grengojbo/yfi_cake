<?php
/* /app/views/helpers/link.php */

App::import('Vendor', 'fpdf/label');

class AveryHelper extends AppHelper {
   
   
    function test(){

        $pdf = new PDF_Label('L7163');
        //$pdf = new PDF_Label('5160');
        //$pdf = new PDF_Label('5161');
        //$pdf = new PDF_Label('5162');
        //$pdf = new PDF_Label('5163');
        //$pdf = new PDF_Label('6082');
        $pdf->AddPage();

// Print labels
for($i=1;$i<=20;$i++) {
    $text = sprintf("%s\n%s\n%s\n%s %s, %s", "Laurent $i", 'Immeuble Toto', 'av. Fragonard', '06000', 'NICE', 'FRANCE');
    $pdf->Add_Label($text);
}

$pdf->Output();

    }
   
}

?>