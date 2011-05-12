<?php
class FreeradiusComponent extends Object {


    //We create an array containing all the includes:
    var $includes   = array();
    //We create an array containing all the VENDORS:
    var $vendors   = array();

   function getVendors(){
        $this->_getVendors();
        $vendors = array_keys($this->vendors);
        //sort($vendors);
        return ($vendors);
    }

    function getAttributes($vendor){
        $this->_getVendors();
        return $this->vendors[$vendor];
    }

    //-------------------------------------------------------
    //------- REALMS CRUD FUNCTIONS -------------------------
    //-------------------------------------------------------

    function realm_add($realm_name){

        Configure::load('yfi');
        $realm_file = Configure::read('freeradius.realms_file');

        //Get the list of realms
        $list_of_realms = $this->_get_list_of_realms($realm_file);
        if(!in_array($realm_name,$list_of_realms)){
            //Not there already add it
            $this->_add_realm($realm_name,$realm_file);
        }
    }

    function realm_del($realm_name){
        Configure::load('yfi');
        $realm_file = Configure::read('freeradius.realms_file');
        $this->_del_realm($realm_name,$realm_file);
    }

    function _del_realm($realm_name,$realm_file){

        $content    = file($realm_file);
        $new_content = array();

        $found_flag = false;
        foreach($content as $line){
           
            if(preg_match("/^\s*realm\s+$realm_name/",$line)){

                $found_flag = true;
            }

            if($found_flag != true){
                array_push($new_content,$line);
            }

            //Clear the found flag at the end of the realm
            if(($found_flag == true)&(preg_match('/^\s*}/',$line))){
                $found_flag = false;
            }
        }

        
         // open the file for reading
        if (!$fp = fopen($realm_file, 'w+')){
            // print an error
            print "Cannot open file ($realm_file)";
            // exit the function
            exit;
        }
  
        // if $fp is valid
        if($fp){
            // write the array to the file
            foreach($new_content as $line) { fwrite($fp,$line); }
            // close the file
            fclose($fp);
        }
        //print_r($new_content);

    }


    function _add_realm($realm_name,$realm_file){

        $handle     = fopen($realm_file, 'a');
        $data = "realm $realm_name {\n\n}\n";
        fwrite($handle, $data);
        fclose($handle);
    }

    function _get_list_of_realms($realm_file){
        $realm_list = array();
        $content = file($realm_file);
        foreach($content as $line){

            if(preg_match('/^\s*realm\s+.+/',$line)){
                $line = rtrim($line);
                $pattern =
                $line = preg_replace('/^\s*realm\s+/','',$line);
                $line = preg_replace('/\s+{$/','',$line);
                array_push($realm_list,$line);
            }
        }
        return $realm_list;
    }



    function _getVendors(){

        $vendor_list = array();

        Configure::load('yfi');
        $main_dictionary_file       = Configure::read('freeradius.main_dictionary_file');
        $path_to_dictionary_files   = Configure::read('freeradius.path_to_dictionary_files');

        $this->vendors['Misc'] = array();


        //Prime the includes array
        $this->_look_for_includes($main_dictionary_file);
        //After we have a list of the includes from $this->main_dictionary_file
        //We will build an array by looking for files included inside its includelist
        //If it does not start with "/" we asume it sits under $this->dictionary_directory
        foreach($this->includes as $include_file){

            $this->_look_for_includes($include_file);
        }

        //loop through this includes array and check all the vendors out:
        //The '_look_for_vendors()' function will extract the vendors.
        foreach($this->includes as $include_file){
            $pattern ='/^\/+/';
            if(preg_match($pattern,$include_file)){
                $this->_look_for_vendors($include_file);    
            }else{
                $this->_look_for_vendors($path_to_dictionary_files.$include_file);
            }
        }
        //print_r($this->vendors);        
    }

    
    //---------------------------------------------------------------
    //----- Private function looking for includes in specified file--
    //---------------------------------------------------------------
    function _look_for_includes ($file_to_look_for_includes){

        $lines = file($file_to_look_for_includes);

        foreach($lines as $line){

            $line = rtrim($line);
            $pattern = '/^\s*\$INCLUDE/';
            if(preg_match($pattern,$line)){

                $filename = preg_split("/\s+/",$line);
                #echo $filename[1]."<br>\n";
                array_push($this->includes, $filename[1]);
            }
        }
    }


    //---------------------------------------------------------------
    //----- Private function looking for vendors in specified  file--
    //---------------------------------------------------------------
    function _look_for_vendors ($file_to_look_for_includes){

        $lines = file($file_to_look_for_includes);
        foreach($lines as $line){

            $line = rtrim($line);
            $pattern = '/^\s*VENDOR/';
            if(preg_match($pattern,$line)){

                $vendor = preg_split("/\s+/",$line);
               // echo $vendor[1]."<br>\n";
                $this->vendors[$vendor[1]] = array();
                $this->_add_attributes_to_vendor($file_to_look_for_includes,$vendor[1]);
                return;

            }
        }
        //If there was not a VENDOR 
        //If no vendor was specified, add the attributes under misc
        $this->_add_attributes_to_vendor($file_to_look_for_includes,"Misc");

    }

    function _add_attributes_to_vendor($file_to_look_for_attributes,$vendor){

         $lines = file($file_to_look_for_attributes);
        foreach($lines as $line){

            $line = rtrim($line);
            $pattern = '/^\s*ATTRIBUTE/';
            if(preg_match($pattern,$line)){
                $attribute = preg_split("/\s+/",$line);
               //echo $attribute[1]."<br>\n";
               array_push($this->vendors[$vendor],$attribute[1]);
            }
        }
    }
}



?>