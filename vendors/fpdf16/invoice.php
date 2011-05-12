<?

require_once('fpdf.php');

class PDF_Invoice extends FPDF {

    var $Logo           = 'img/graphics/logo.jpg';       //Default Logo
    var $Title          = 'Set The Title';

    // Constructor
    function PDF_Invoice() {
        parent::FPDF('P', 'mm', 'A4');
    }

    
    //Page header
    function Header( )  //Override FPDF's Method
    {

        $this->SetFont('Times','',12);
        $this->SetDrawColor(0,0,180);
        $this->SetFillColor(119,127,138);
        $this->SetTextColor(50,50,50);
        $this->SetLineWidth(0.1);

        $this->Image(WWW_ROOT.DS.$this->Logo,10,10.5,15,8);
        $this->Cell(0,9,iconv('UTF-8', 'windows-1252',$this->Title),1,0,'C');
        $this->Cell(0,9,date("F j, Y, g:i a"),1,1,'R');
        $this->Ln(10);
        $this->y_pos = $this->GetY();
    }

    //Page footer
    function Footer()   //Override FPDF's Method
    {
        //Position at 1.5 cm from bottom
        $this->SetY(-15);
        //Arial italic 8
        $this->SetFont('Arial','I',8);
        //Page number
        $this->Cell(0,10,iconv('UTF-8', 'windows-1252',gettext('Page ')).'1/1',0,0,'C');   //Invoices should be only one page/user
    }

    // Label for invoice number
    function addInvoiceNumber( $invoice_number ){

        $r1  = $this->w - 80;
        $r2  = $r1 + 68;
        $y1  = 25;
        $y2  = 6 + 2;
        $mid = ($r1 + $r2 ) / 2;
    
        $texte  = iconv('UTF-8', 'windows-1252',gettext("Invoice Nr : ")).$invoice_number;
        $szfont = 12;
        $loop   = 0;
    
        while ( $loop == 0 ){
            $this->SetFont( "Helvetica", "B", $szfont );
            $sz = $this->GetStringWidth( $texte );
            if ( ($r1+$sz) > $r2 )
              $szfont --;
            else
            $loop ++;
        }
        $this->SetLineWidth(0.1);
        $this->SetFillColor(192);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 2.5, 'DF');
        $this->SetXY( $r1+1, $y1+2);
        $this->Cell($r2-$r1 -1,5, $texte, 0, 0, "C" );
    }


    //Client detail window
    function addCustomerDetail($user)
    {
        $r1  = $this->w - 80;
        $r2  = $r1 + 68;
        $y1  = 37;
        $y2  = $y1 ;
        $mid = $y1 + ($y2 / 2);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
        $this->Line( $r1, 42, $r2, 42);
        $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell(10,5, iconv('UTF-8', 'windows-1252',gettext("Customer Info")), 0, 0, "C");


        $this->SetXY( $r1+4 , $y1+6 );
        $this->SetFillColor(255);

        $this->addCustomerDetailItem(iconv('UTF-8', 'windows-1252',gettext("Username")),iconv('UTF-8', 'windows-1252',$user['username']));
        $this->addCustomerDetailItem(iconv('UTF-8', 'windows-1252',gettext("Name")),iconv('UTF-8', 'windows-1252',$user['name']));
        $this->addCustomerDetailItem(iconv('UTF-8', 'windows-1252',gettext("Surname")),iconv('UTF-8', 'windows-1252',$user['surname']));
        $this->addCustomerDetailItem(iconv('UTF-8', 'windows-1252',gettext("Phone")),iconv('UTF-8', 'windows-1252',$user['phone']));
        $this->addCustomerDetailItem(iconv('UTF-8', 'windows-1252',gettext("e-Mail")),iconv('UTF-8', 'windows-1252',$user['email']));
        $this->addCustomerDetailItem(iconv('UTF-8', 'windows-1252',gettext("Address")),iconv('UTF-8', 'windows-1252',$user['address']));
        
    }


    // Access Provider Detail
    function addAccessProvider( $ap_detail)
    {
        $x1 = 10;
        $y1 = 25;
        $max_address_width = '100';
        $outline = 0;
        
        //AP Name
        $this->SetXY( $x1, $y1 );
        $this->SetFont('Arial','B',12);
        $this->Cell($max_address_width, 5,iconv('UTF-8', 'windows-1252',$ap_detail['name']),$outline,2);  //Name of AP

        //AP Address
        $this->SetFont('Arial','B',10);
        $this->Cell($max_address_width,4,iconv('UTF-8', 'windows-1252',gettext('Address')),$outline,2);
        $this->SetFont('Courier','',8);
        $this->MultiCell($max_address_width,3,iconv('UTF-8', 'windows-1252',$ap_detail['address']),$outline,2);

        //Contact Detail
        $this->SetFont('Arial','B',8);
        $this->Cell($max_address_width,4,iconv('UTF-8', 'windows-1252',gettext('Contact Detail')),$outline,2);
        //url
        if($ap_detail['url'] != ''){
            $this->SetFont('Courier','I',8);
            $this->SetTextColor(0,0,255);
            $this->Cell($max_address_width,3,$ap_detail['url'],$outline,2);
        }
        //email
        if($ap_detail['email'] != ''){
            $this->SetFont('Courier','I',8);
            $this->SetTextColor(0,0,255);
            $this->Cell($max_address_width,3,$ap_detail['email'],$outline,2);
        }

        $this->SetTextColor(0);

        //phone
        if($ap_detail['phone'] != ''){
            $this->SetFont('Courier','I',8);
            $this->Cell($max_address_width,3,$ap_detail['phone'].' '.iconv('UTF-8', 'windows-1252',gettext('(phone)')),$outline,2);
        }

        //cell
        if($ap_detail['phone'] != ''){
            $this->SetFont('Courier','I',8);
            $this->Cell($max_address_width,3,$ap_detail['cell'].' '.iconv('UTF-8', 'windows-1252',gettext('(cell)')),$outline,2);
        }

         //fax
        if($ap_detail['phone'] != ''){
            $this->SetFont('Courier','I',8);
            $this->Cell($max_address_width,3,$ap_detail['fax'].' '.iconv('UTF-8', 'windows-1252',gettext('(fax)')),$outline,2);
        }

        //------You may add additional fields here----------


        //-------------------------------------------------

    }

    function addCustomerDetailItem($item,$value){

        $r1  = $this->w - 80;
        $this->SetX( $r1+4);
        $this->SetFont( "Helvetica", "B", 6);
        $this->Cell(20,3,$item, 0,0, ":");
        $this->SetFont( "Helvetica", "", 6);
        $this->MultiCell(40,3,$value, 0,1, "L");

    }


    function addBillingDetail($detail){

        $this->addBillingdDetailItem(iconv('UTF-8', 'windows-1252',gettext("Start date")),$detail['start_date'],200);
        $this->addBillingdDetailItem(iconv('UTF-8', 'windows-1252',gettext("End date")),$detail['end_date'],165);
        $this->addBillingdDetailItem(iconv('UTF-8', 'windows-1252',gettext("Billing Plan")),$detail['name'],130);
        $this->ln(8);
    }

    function addBillingdDetailItem($item,$value,$start_position){

        $r1  = $this->w - $start_position;
        $r2  = $r1 + 35;
        $y1  = 65;
        $y2  = 10 ;
        $mid = $y1 + ($y2 / 2);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
        $this->Line( $r1, $mid, $r2, $mid);
        $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell(10,5, $item, 0, 0, "C");

        $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+5 );
        $this->SetFont( "Helvetica", "", 6);
        $this->Cell(10,5,$value, 0,0, "C");
        $this->SetFont( "Helvetica", "", 10);
    }


    function addUsageTotals($y_pos,$items){

       $item_width = 35;
       $this->y_pos = $y_pos;

        //if it is to low, make another page
        if($this->y_pos > 240){
            $this->AddPage();
        }

        $r1  = $this->w - 200;
        $r2  = $r1 + 80;
        $y1  = $this->y_pos + 10;
        $y2  = 40 ;
        $mid = $y1 + ($y2 / 2);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
        $this->Line( $r1, $y1+4, $r2, $y1+4);
        $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell(10,5,iconv('UTF-8', 'windows-1252',gettext("Usage Sumary")), 0, 0, "C");

        $this->SetXY( $r1 + 1, $y1+5 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Item")), 0,0, "L");
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Value")), 0,1, "R");

        $fill = 1;
        $this->SetFillColor(208,208,208);
        foreach($items as $item){
            $fill = !($fill);
            $this->SetX( $r1 + 1);
            $this->SetFont( "Helvetica", "", 8);
            $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext($item[0])), 0,0, "L",$fill);
            $this->Cell($item_width,4,$item[1], 0,1, "R",$fill);
        }
        $this->SetY($this->GetY()+5);
    }

    function addStats($y_pos,$items){

       $item_width = 35;
       $this->y_pos = $y_pos;

        //if it is to low, make another page
        if($this->y_pos > 240){
            $this->AddPage();
        }

        $r1  = $this->w - 90;
        $r2  = $r1 + 80;
        $y1  = $this->y_pos + 10;
        $y2  = 30 ;
        $mid = $y1 + ($y2 / 2);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
        $this->Line( $r1, $y1+4, $r2, $y1+4);
        $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell(10,5,iconv('UTF-8', 'windows-1252',gettext("Stats")), 0, 0, "C");


        $this->SetXY( $r1 + 1, $y1+5 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Item")), 0,0, "L");
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Value")), 0,1, "R");

        $fill = 1;
        $this->SetFillColor(208,208,208);
        foreach($items as $item){
            $fill = !($fill);
            $this->SetX( $r1 + 1);
            $this->SetFont( "Helvetica", "", 8);
            $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext($item[0])), 0,0, "L",$fill);
            $this->Cell($item_width,4,$item[1], 0,1, "R",$fill);
        }
        $this->SetY($this->GetY()+5);
    }

    function addExtraServices($y_pos,$items){

       $item_width = 45;
       $this->y_pos = $y_pos;

        //if it is to low, make another page
        if($this->y_pos > 240){
            $this->AddPage();
        }

        $r1  = $this->w - 200;
        $r2  = $r1 + 190;
        $y1  = $this->y_pos + 10;
        $y2  = ((count($items)+2)*4)+5 ;     //How high must this frame be
        $mid = $y1 + ($y2 / 2);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
        $this->Line( $r1, $y1+4, $r2, $y1+4);
        $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell(10,5,iconv('UTF-8', 'windows-1252',gettext("Extra Services")), 0, 0, "C");

        //Put the headings:
        $this->SetXY( $r1 + 1, $y1+5 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Title")), 0,0, "L");
        $this->Cell(($item_width * 2),4,iconv('UTF-8', 'windows-1252',gettext("Description")), 0,0, "L");
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Amount")), 0,1, "R");

        $fill = 1;
        $this->SetFillColor(208,208,208);
        foreach($items as $item){
            $fill = !($fill);
            $this->SetX( $r1 + 1);
            $this->SetFont( "Helvetica", "", 8);
            $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext($item[0])), 0,0, "L",$fill);
            $this->Cell(($item_width*2),4,iconv('UTF-8', 'windows-1252',gettext($item[1])), 0,0, "L",$fill);
            $this->Cell($item_width,4,$item[2], 0,1, "R",$fill);
        }
        $this->SetY($this->GetY()+5);



    }

    function addCostBreakdown($y_pos,$items,$outstanding, $currency){

       $item_width = 45;
       $this->y_pos = $y_pos;

        //if it is to low, make another page
        if($this->y_pos > 240){
            $this->AddPage();
        }

        $r1  = $this->w - 200;
        $r2  = $r1 + 190;
        $y1  = $this->y_pos + 10;
        $y2  = 50 ;
        $mid = $y1 + ($y2 / 2);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
        $this->Line( $r1, $y1+4, $r2, $y1+4);
        $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell(10,5,iconv('UTF-8', 'windows-1252',gettext("Cost Breakdown")), 0, 0, "C");

        //Put the headings:
        $this->SetXY( $r1 + 1, $y1+5 );
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Item")), 0,0, "L");
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Value")), 0,0, "L");
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Usage")), 0,0, "L");
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Amount")), 0,1, "R");

        /*
        $items = array(
                        
                        array('Subscription','100','1','100'),
                        array('Normal Cost/Second','0.00005','500','30'),
                        array('Extra Cost/Second','0.00005','500','30'),
                        array('Normal Cost/Byte','0.00005','500','30'),
                        array('Extra Cost/Byte','0.00005','500','30'),
                        array('Total Usage Discount','0.00005','500','30'),
                        array('Extra Services','0.00005','500','30'),
                        array('Tax','0.00005','500','30'),
                        array('Outstanding','500','1','500')
                );
        */

        $next = 5;
        $fill = 1;
        $this->SetFillColor(208,208,208);
        foreach($items as $item){
            $fill = !($fill);
            $this->SetX( $r1 + 1);
            $this->SetFont( "Helvetica", "", 8);
            $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext($item[0])), 0,0, "L",$fill);
            $this->Cell($item_width,4,$item[1], 0,0, "L",$fill);
            $this->Cell($item_width,4,$item[2], 0,0, "L",$fill);
            $this->Cell($item_width,4,$item[3], 0,1, "R",$fill);
        }
        $this->SetY( $this->GetY()+ 2);
        $this->SetX( $r1 + 1);
        $this->Cell($item_width,4,"", 0,0, "L");
        $this->Cell($item_width,4,"", 0,0, "L");
        $this->SetFont( "Helvetica", "B", 10);
        $this->Cell($item_width,4,iconv('UTF-8', 'windows-1252',gettext("Total in $currency")), 1,0, "R",1);
        $this->Cell($item_width,4,$outstanding, 1,1, "R",1);
        $this->SetFillColor(0,0,0);
    }



    //-------------------------------------------------------------------------------------
    // private functions
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' or $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2f %.2f m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2f %.2f l', $xc*$k,($hp-$y)*$k ));

        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2f %.2f l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2f %.2f l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2f %.2f l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
                        $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
    //---------------------------------------------------------------------------------------


}


?>