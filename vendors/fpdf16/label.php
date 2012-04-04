<?

require_once('fpdf.php');

class PDF_Label extends FPDF {

    
    //------Private properties---------------
    var $_Avery_Name    = '';   // Name of format
    var $_Margin_Left   = 0;    // Left margin of labels
    var $_Margin_Top    = 0;    // Top margin of labels
    var $_X_Space       = 0;    // Horizontal space between 2 labels
    var $_Y_Space       = 0;    // Vertical space between 2 labels
    var $_X_Number      = 0;    // Number of labels horizontally
    var $_Y_Number      = 0;    // Number of labels vertically
    var $_Width         = 0;    // Width of label
    var $_Height        = 0;    // Height of label
    var $_Char_Size     = 10;   // Character size
    var $_Line_Height   = 10;   // Default line height
    var $_Metric        = 'mm'; // Type of metric for labels.. Will help to calculate good values
    var $_Metric_Doc    = 'mm'; // Type of metric for the document
    var $_Font_Name     = 'Arial';  // Name of the font
    var $_COUNTX        = 1;
    var $_COUNTY        = 1;

    var $Logo           = 'img/graphics/log.jpg';       //Default Logo

   // var $_Padding;      // Padding
 
      // List of label formats
    var $_Avery_Labels = array(
        '5160' => array(
                            'name'          => '5160',
                            'paper-size'    => 'letter',
                            'metric'        => 'mm',
                            'marginLeft'    => 1.762,    
                            'marginTop'     => 10.7,  
                            'NX'            => 3,    
                            'NY'            => 10,
                            'SpaceX'        => 3.175,
                            'SpaceY'        => 0,
                            'width'         => 66.675,
                            'height'        => 25.4,
                            'font-size'     => 8
        ),
        '5161' => array(
                            'name'          => '5161',
                            'paper-size'    => 'letter',
                            'metric'        => 'mm',
                            'marginLeft'    => 0.967,
                            'marginTop'     => 10.7,
                            'NX'            => 2,
                            'NY'            => 10,
                            'SpaceX'        => 3.967,
                            'SpaceY'        => 0,
                            'width'         => 101.6,
                            'height'        => 25.4,
                            'font-size'     => 8
        ),
        '5162' => array(
                            'name'          => '5162',
                            'paper-size'    => 'letter',
                            'metric'        => 'mm',
                            'marginLeft'    => 0.97,
                            'marginTop'     => 20.224,
                            'NX'            => 2,
                            'NY'            => 7,
                            'SpaceX'        => 4.762,
                            'SpaceY'        => 0,
                            'width'         => 100.807,
                            'height'        => 35.72,
                            'font-size'     => 8
        ),
        '5163' => array(
                            'name'          => '5163',
                            'paper-size'    => 'letter',
                            'metric'        => 'mm',
                            'marginLeft'    => 1.762,
                            'marginTop'     => 10.7,
                            'NX'            => 2,
                            'NY'            => 5,
                            'SpaceX'        => 3.175,
                            'SpaceY'        => 0,
                            'width'         => 101.6,
                            'height'        => 50.8,
                            'font-size'     => 8
        ),
        '5164' => array(
                            'name'          => '5164',
                            'paper-size'    => 'letter',
                            'metric'        => 'in',
                            'marginLeft'    => 0.148,
                            'marginTop'     => 0.5,
                            'NX'            => 2,
                            'NY'            => 3,
                            'SpaceX'        => 0.2031,
                            'SpaceY'        => 0,
                            'width'         => 4.0,
                            'height'        => 3.33,
                            'font-size'     =>12
        ),
        '5881'  => array(
                            'name'          =>  '5881',
                            'paper-size'    =>  'letter',
                            'metric'        =>  'mm',
                            'marginLeft'    =>  19, 
                            'marginTop'     =>  20,
                            'NX'            =>  2, 
                            'NY'            =>  4, 
                            'SpaceX'        =>  20, 
                            'SpaceY'        =>  0,
                            'width'         =>  90,
                            'height'        =>  68,
                            'font-size'     =>  9
        ),
        '6082'  => array(
                            'name'          =>  '6082',
                            'paper-size'    =>  'letter',
                            'metric'        =>  'mm',
                            'marginLeft'    =>  3.77,
                            'marginTop'     =>  17,
                            'NX'            =>  2,
                            'NY'            =>  7,
                            'SpaceX'        =>  0,
                            'SpaceY'        =>  5.16,
                            'width'         =>  101.6,
                            'height'        =>  31.4,
                            'font-size'     =>  9
        ),
        '6083'  => array(
                            'name'          =>  '6083' ,
                            'paper-size'    =>  'letter',
                            'metric'        =>  'mm',
                            'marginLeft'    =>  3.77,
                            'marginTop'     =>  12.5,
                            'NX'            =>  2,
                            'NY'            =>  5,
                            'SpaceX'        =>  0,
                            'SpaceY'        =>  5.16,
                            'width'         =>  101.6,
                            'height'        =>  48.8,
                            'font-size'     =>  9
        ),

        '8600' => array(
                            'name'          => '8600',
                            'paper-size'    => 'letter',
                            'metric'        => 'mm',
                            'marginLeft'    => 7.1,
                            'marginTop'     => 19,
                            'NX'            => 3,
                            'NY'            => 10,
                            'SpaceX'        => 9.5,
                            'SpaceY'        => 3.1,
                            'width'         => 66.6,
                            'height'        => 25.4,
                            'font-size'     => 8
        ),
        'L7163'=> array(
                            'name'          => 'L7163',
                            'paper-size'    => 'A4',
                            'metric'        => 'mm',
                            'marginLeft'    => 5,
                            'marginTop'     => 15,
                            'NX'            => 2,
                            'NY'            => 7,
                            'SpaceX'        => 2,  //Change from 25 to 2 to prevent the huge margin
                            'SpaceY'        => 0,
                            'width'         => 99.1,
                            'height'        => 38.1,
                            'font-size'     =>9
        )
    );

    // Constructor
    function PDF_Label($format, $unit='mm', $posX=1, $posY=1) {
        if (is_array($format)) {
            // Custom format
            $Tformat = $format;
        } else {
            // Built-in format
            if (!isset($this->_Avery_Labels[$format]))
                $this->Error('Unknown label format: '.$format);
            $Tformat = $this->_Avery_Labels[$format];
        }

        parent::FPDF('P', $unit, $Tformat['paper-size']);
        $this->_Metric_Doc = $unit;
        $this->_Set_Format($Tformat);
        $this->SetFont('Arial');
        $this->SetMargins(0,0);
        $this->SetAutoPageBreak(false);
        $this->_COUNTX = $posX-2;
        $this->_COUNTY = $posY-1;
    }

    function _Set_Format($format) {
        $this->_Margin_Left    = $this->_Convert_Metric($format['marginLeft'], $format['metric']);
        $this->_Margin_Top    = $this->_Convert_Metric($format['marginTop'], $format['metric']);
        $this->_X_Space     = $this->_Convert_Metric($format['SpaceX'], $format['metric']);
        $this->_Y_Space     = $this->_Convert_Metric($format['SpaceY'], $format['metric']);
        $this->_X_Number     = $format['NX'];
        $this->_Y_Number     = $format['NY'];
        $this->_Width         = $this->_Convert_Metric($format['width'], $format['metric']);
        $this->_Height         = $this->_Convert_Metric($format['height'], $format['metric']);
        $this->Set_Font_Size($format['font-size']);
        $this->_Padding        = $this->_Convert_Metric(3, 'mm');
    }

    // convert units (in to mm, mm to in)
    // $src must be 'in' or 'mm'
    function _Convert_Metric($value, $src) {
        $dest = $this->_Metric_Doc;
        if ($src != $dest) {
            $a['in'] = 39.37008;
            $a['mm'] = 1000;
            return $value * $a[$dest] / $a[$src];
        } else {
            return $value;
        }
    }

    // Give the line height for a given font size
    function _Get_Height_Chars($pt) {
        $a = array(6=>2, 7=>2.5, 8=>3, 9=>4, 10=>5, 11=>6, 12=>7, 13=>8, 14=>9, 15=>10);
        if (!isset($a[$pt]))
            $this->Error('Invalid font size: '.$pt);
        return $this->_Convert_Metric($a[$pt], 'mm');
    }

    // Sets the character size
    // This changes the line height too
    function Set_Font_Size($pt) {
        $this->_Line_Height = $this->_Get_Height_Chars($pt);
        $this->SetFontSize($pt);
    }

    // Print a label
    function Add_Label($label_detail) {

        $img_space = 10;

        $this->_COUNTX++;
        if ($this->_COUNTX == $this->_X_Number) {
            // Row full, we start a new one
            $this->_COUNTX=0;
            $this->_COUNTY++;
            if ($this->_COUNTY == $this->_Y_Number) {
                // End of page reached, we start a new one
                $this->_COUNTY=0;
                $this->AddPage();
            }
        }

        $_PosX = $this->_Margin_Left + $this->_COUNTX*($this->_Width+$this->_X_Space) + $this->_Padding;
        $_PosY = $this->_Margin_Top + $this->_COUNTY*($this->_Height+$this->_Y_Space) + $this->_Padding;

       // print("Positions X $_PosX Y $_PosY <br>\n");
        $this->SetXY($_PosX, $_PosY);
      //  

        $this->Set_Font_Size('10');
        $this->Cell($this->_Width-$this->_Margin_Left, 5, iconv('UTF-8', 'windows-1252',gettext('Internet Access Voucher')), 0, 2, "C");

        //Get the X position
        $x_after_heading = $this->GetX();
        $y_after_heading = $this->GetY();

        $this->Image(WWW_ROOT.DS.$this->Logo,null,null,8);

       // $this->Set_Font_Size('6');
        //Set the location to start the details
        $this->SetXY($x_after_heading+$img_space, $y_after_heading);
        
        $detail_width   = $this->_Width-$this->_Margin_Left-$img_space;
        $field_width    = $detail_width / 2;

        $this->_add_pair($field_width,array('key'=> iconv('UTF-8', 'windows-1252',gettext('Username')), 'value'     => iconv('UTF-8', 'windows-1252',$label_detail['username'])));
        $this->_add_pair($field_width,array('key'=> iconv('UTF-8', 'windows-1252',gettext('Password')), 'value'     => iconv('UTF-8', 'windows-1252',$label_detail['password'])));
        $this->_add_pair($field_width,array('key'=> iconv('UTF-8', 'windows-1252',gettext('Profile')),  'value'     => iconv('UTF-8', 'windows-1252',$label_detail['profile'])));
        //Disgard the entries that does not feature a days from first login...
        if(array_key_exists('days_valid',$label_detail)){
            $this->_add_pair(
                    $field_width,
                    array(
                        'key'       => iconv('UTF-8', 'windows-1252',gettext('Valid for')),
                        'value'     => iconv('UTF-8', 'windows-1252',$label_detail['days_valid'])
                    )
            );
        }
        $this->_add_pair($field_width,array('key'=> iconv('UTF-8', 'windows-1252',gettext('Expiry date')),'value'   => iconv('UTF-8', 'windows-1252',$label_detail['expiry_date'])));
        
        //$this->MultiCell($this->_Width-$this->_Margin_Left-$img_space, $this->_Line_Height, $text,1);
    }


    function _add_pair($field_width,$pair){

        
        $this->SetFont('Arial','I',6);
        $this->Cell($field_width, 3, $pair['key'], 0, 0, "L");
        $this->SetFont('Arial','B',6);
        $this->Cell($field_width, 3, $pair['value'], 0, 2, "L");
        $this->SetFont('Arial','',6);
        $this->SetX($this->getX()-$field_width);

    }

}

?>
