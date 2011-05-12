<?

require_once('fpdf.php');

class PDF_Generic extends FPDF {

    var $Logo           = 'img/graphics/log.jpg';       //Default Logo
    var $Title          = 'Set The Title';
    var $Language       = 'en';

    // Constructor
    function PDF_Generic() {

      //  $this->AliasNbPages();
       // $this->SetFont('Times','',12);
        parent::FPDF('P', 'mm', 'A4');

    }

    
    //Page header
    function Header( )  //Override FPDF's Method
    {

        //--Language Specifics--
        if($this->Language == 'th_TH'){
            $this->AddFont('Loma','','loma.php');
            $font_type      = 'Loma';
            $font_encode    = 'cp874';
        }else{
            $font_type      = 'Times';
            $font_encode    = 'windows-1252';
        }
        //-- END Language Specifics --

        $this->SetFont($font_type,'',12);
        $this->SetDrawColor(0,0,180);
        $this->SetFillColor(119,127,138);
        $this->SetTextColor(50,50,50);
        $this->SetLineWidth(0.1);

        $this->Image(WWW_ROOT.DS.$this->Logo,10,10.5,15,8);
        $this->Cell(0,9,iconv('UTF-8',$font_encode,$this->Title),1,0,'C');
        $this->Cell(0,9,date("F j, Y, g:i a"),1,1,'R');
        $this->Ln(10);
        $this->y_pos = $this->GetY();
    }

    //Page footer
    function Footer()   //Override FPDF's Method
    {
        //--Language Specifics--
        if($this->Language == 'th_TH'){
            $font_type      = 'Loma';
            $font_encode    = 'cp874';
            $font_format    = '';
        }else{
            $font_type      = 'Arial';
            $font_encode    = 'windows-1252';
            $font_format    = 'I';
        }
        //-- END Language Specifics --


        //Position at 1.5 cm from bottom
        $this->SetY(-15);
        //Arial italic 8
        $this->SetFont($font_type,$font_format,8);
        //Page number
        $this->Cell(0,10,iconv('UTF-8', $font_encode,gettext('Page ')).$this->PageNo().'/{nb}',0,0,'C');
    }

    //Profile detail window
    function addProfileDetail($voucher_data){

        //--Language Specifics--
        if($this->Language == 'th_TH'){
            $font_type      = 'Loma';
            $font_encode    = 'cp874';
            $font_format    = '';
        }else{
            $font_type      = 'Arial';
            $font_encode    = 'windows-1252';
            $font_format    = 'I';
        }
        //-- END Language Specifics --


        $r1  = $this->w - 100;
        $r2  = $r1 + 88;
        $y1  = 37;
        $y2  = $y1 ;
        $mid = $y1 + ($y2 / 2);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
        $this->Line( $r1, 42, $r2, 42);
        $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
        $this->SetFont( $font_type , $font_format, 10);
        $this->Cell(10,5,iconv('UTF-8', $font_encode,gettext("Profile attributes")), 0, 0, "C");

        $this->SetXY( $r1+4 , $y1+5 );

        foreach($voucher_data as $item){
            $this->addProfileDetailItem($item['attribute'],$item['value']);
        }
        $this->SetXY( $r1+4 , $y1+5 );
    }

    function addProfileDetailItem($item,$value){

        //--Language Specifics--
        if($this->Language == 'th_TH'){
            $font_type      = 'Loma';
            $font_encode    = 'cp874';
            $font_format_b  = '';
        }else{
            $font_type      = 'Helvetica';
            $font_encode    = 'windows-1252';
            $font_format_b  = 'B';
        }
        //-- END Language Specifics --

        $r1 = $this->w -100;
        $this->SetX( $r1+4);
        $this->SetFont( "$font_type", "$font_format_b", 6);
        $this->Cell(40,3,$item, 0,0, "L");
        $this->SetFont( "$font_type", "", 6);
        $this->Cell(40,3,$value, 0,1, "L");

    }

    //Profile name
    function addProfileName($profile_name){

        //--Language Specifics--
        if($this->Language == 'th_TH'){
            $font_type      = 'Loma';
            $font_encode    = 'cp874';
            $font_format_b  = '';
        }else{
            $font_type      = 'Helvetica';
            $font_encode    = 'windows-1252';
            $font_format_b  = 'B';
        }
        //-- END Language Specifics --

        $r1  = $this->w - 100;
        $r2  = $r1 + 88;
        $y1  = 25;
        $y2  = 6 + 2;
        $mid = ($r1 + $r2 ) / 2;
    
        $texte  = iconv('UTF-8', $font_encode,gettext("Profile: ").$profile_name);    
        $szfont = 12;
        $loop   = 0;
    
        while ( $loop == 0 )
        {
            $this->SetFont( $font_type, $font_format_b, $szfont );
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


    // Access Provider Detail
    function addAccessProvider( $ap_detail)
    {
         //--Language Specifics--
        if($this->Language == 'th_TH'){
            $font_type_1    = 'Loma';
            $font_type_2    = 'Loma';
            $font_encode    = 'cp874';
            $font_format_b  = '';
            $font_format_i  = '';
        }else{
            $font_type_1    = 'Arial';
            $font_type_2    = 'Courier';
            $font_encode    = 'windows-1252';
            $font_format_b  = 'B';
            $font_format_i  = 'I';
        }
        //-- END Language Specifics --

        $x1 = 10;
        $y1 = 25;
        $max_address_width = '100';
        $outline = 0;
        
        //AP Name
        $this->SetXY( $x1, $y1 );
        $this->SetFont($font_type_1,$font_format_b,12);
        $this->Cell($max_address_width, 5,$ap_detail['name'],$outline,2);  //Name of AP

        //AP Address
        $this->SetFont($font_type_1,$font_format_b,10);
        $this->Cell($max_address_width,4,iconv('UTF-8', $font_encode,gettext('Address')),$outline,2);
        $this->SetFont($font_type_2,'',8);
        $this->MultiCell($max_address_width,3,iconv('UTF-8', $font_encode,$ap_detail['address']),$outline,2);

        //Contact Detail
        $this->SetFont($font_type_1,$font_format_b,8);
        $this->Cell($max_address_width,4,iconv('UTF-8', $font_encode,gettext('Contact Detail')),$outline,2);
        //url
        if($ap_detail['url'] != ''){
            $this->SetFont($font_type_2,$font_format_i,8);
            $this->SetTextColor(0,0,255);
            $this->Cell($max_address_width,3,$ap_detail['url'],$outline,2);
        }
        //email
        if($ap_detail['email'] != ''){
            $this->SetFont($font_type_2,$font_format_i,8);
            $this->SetTextColor(0,0,255);
            $this->Cell($max_address_width,3,$ap_detail['email'],$outline,2);
        }

        $this->SetTextColor(0);

        //phone
        if($ap_detail['phone'] != ''){
            $this->SetFont($font_type_2,$font_format_i,8);
            $this->Cell($max_address_width,3,$ap_detail['phone'].' '.iconv('UTF-8', $font_encode,gettext('(phone)')),$outline,2);
        }

        //cell
        if($ap_detail['phone'] != ''){
            $this->SetFont($font_type_2,$font_format_i,8);
            $this->Cell($max_address_width,3,$ap_detail['cell'].' '.iconv('UTF-8', $font_encode,gettext('(cell)')),$outline,2);
        }

         //fax
        if($ap_detail['phone'] != ''){
            $this->SetFont($font_type_2,$font_format_i,8);
            $this->Cell($max_address_width,3,$ap_detail['fax'].' '.iconv('UTF-8', $font_encode,gettext('(fax)')),$outline,2);
        }

        //------You may add additional fields here----------


        //-------------------------------------------------

    }


    //This will loop throug the vouchers, creating them
    function addVouchers($voucher_data)
    {
        //Initial positioning
        $this->left_col = 1;
        $this->SetY(75);
        $this->Ln();

      //  print_r($voucher_data);

        foreach(array_keys($voucher_data) as $voucher_key){

            $this->addVoucher($voucher_key,$voucher_data[$voucher_key]);
            $this->left_col = !($this->left_col);
        }
    }

    //Voucher detail window
    function addVoucher($username,$voucher)
    {

         //--Language Specifics--
        if($this->Language == 'th_TH'){
            $font_type      = 'Loma';
            $font_encode    = 'cp874';
            $font_format_b  = '';
            $font_format_i  = '';
        }else{
            $font_type      = 'Helvetica';
            $font_encode    = 'windows-1252';
            $font_format_b  = 'B';
            $font_format_i  = 'I';
        }
        //-- END Language Specifics --

        $text_size      = 6;    //Up this value to increase the text inside the voucher
        $cell_height    = 3;    //Up this value to increase the space between the lines in the voucher
        //Experiment
        if($this->GetY() > 240){
            $this->AddPage();
        }

        //Get the current pos
        $x_curr = $this->GetX();
        $y_curr = $this->GetY();

        if($this->left_col){
            $r1  = $this->w - 200;
        }else{
            $r1  = $this->w - 100;
        }

        $r2  = $r1 + 88;
        $y1  = $y_curr;
        $y2  = 20 ;         //Up this value to increase the size of the woucher's frame
        $mid = $y1 + ($y2 / 2);
        $this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
        $this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
        $this->SetFont( $font_type, $font_format_b, 10);

        $this->Cell(10,5, iconv('UTF-8', $font_encode,$this->Title), 0, 2, "C");
        $x_p = $this->GetX()-18;

        $this->SetX($x_p);
        $this->SetFont( $font_type, $font_format_i, $text_size);
        $this->Cell(22,$cell_height, iconv('UTF-8', $font_encode,gettext("Username")), 0, 0, "L");

        $this->SetFont( $font_type, $font_format_b, $text_size);
        $this->Cell(30,$cell_height, $username, 0, 2, "L");

        //--Password----
        $this->SetFont( $font_type, $font_format_i, $text_size);
        $this->SetX($x_p);
        $this->Cell(22,$cell_height,iconv('UTF-8', $font_encode,gettext("Password")) , 0, 0, "L");

        $this->SetFont($font_type, $font_format_b, $text_size);
        $this->Cell(30,$cell_height, $voucher['password'], 0, 2, "L");

        //---Duration---
        $this->SetFont( $font_type, $font_format_i, $text_size);
        $this->SetX($x_p);
        $this->Cell(22,$cell_height,iconv('UTF-8', $font_encode,gettext("Valid for")) , 0, 0, "L");

        $this->SetFont( $font_type, $font_format_b, $text_size);
        $this->Cell(30,$cell_height, iconv('UTF-8', $font_encode,$voucher['days_valid']), 0, 2, "L");

        //---Expiry Date---
         $this->SetFont( $font_type, $font_format_i, $text_size);
        $this->SetX($x_p);
        $this->Cell(22,$cell_height,iconv('UTF-8', $font_encode,gettext("Expiry date")) , 0, 0, "L");

        $this->SetFont( $font_type, $font_format_b, $text_size);
        $this->Cell(30,$cell_height, $voucher['expiry_date'], 0, 2, "L");

        $this->Ln();
        $this->Image(WWW_ROOT.DS.$this->Logo,$r1+3,$y_curr+5,15,12);

        if(!($this->left_col)){
            $this->SetY( $y_curr+25);   //Up this value to increase the size of the woucher's frame (in relation to the top one)
        }else{
            $this->SetY( $y_curr);
        }
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