<?php
class PptpdComponent extends Object {

    function show_next_nas(){

       return $this->_show_next_nas();
    }

    function show_nas($ip){

        Configure::load('yfi');
        $chap_file  = Configure::read('pptpd.chap_secrets');
        $base_name  = Configure::read('pptpd.yfi_nas_base_name');
        $server_ip  = Configure::read('pptpd.server_ip');
        $content    = file($chap_file);

        $match_line = '';

        foreach($content as $line){

            if(preg_match("/^$base_name/",$line)){

                if(preg_match("/$ip/",$line)){
                    $line = ltrim($line);
                    $line = rtrim($line);
                    $match_line = $line;
                }
            }
            //print("Line".$line."<br>\n");

        }

        if($match_line != ''){
            $pieces     = explode(" ",$match_line);
            $client     = $pieces[0];
            $server     = $pieces[1];
            $secret     = $pieces[2];
            $ip         = $pieces[3];
            return array('client'=> $client,'server' =>$server,'secret'=> $secret,'ip' => $ip,'server_ip' => $server_ip);
        }

        return;
    }


    function del_nas($ip){

        Configure::load('yfi');
        $chap_file  = Configure::read('pptpd.chap_secrets');
        $base_name  = Configure::read('pptpd.yfi_nas_base_name');

        $content    = file($chap_file);

        $new_content = array();

        foreach($content as $line){

            $line = ltrim($line);
            $match_found = false;
            if(preg_match("/^$base_name/",$line)){

                if(preg_match("/$ip/",$line)){
                    $match_found = true;
                }
            }
            if($match_found == false){
                array_push($new_content,$line);
            }
        }

         // open the file for reading
        if (!$fp = fopen($chap_file, 'w+')){
            // print an error
            print "Cannot open file ($chap_file)";
            // exit the function
            exit;
        }
  
        // if $fp is valid
        if($fp)
        {
            // write the array to the file
            foreach($new_content as $line) { fwrite($fp,$line); }
            // close the file
            fclose($fp);
        }
    }

    function add_next_nas(){

        $next_nas = $this->_show_next_nas();
        
        Configure::load('yfi');
        $chap_file  = Configure::read('pptpd.chap_secrets');
        $handle     = fopen($chap_file, 'a');
        $password   = $this->_generatePassword();
        $data = $next_nas['client'].' pptpd '.$password.' '.$next_nas['ip']."\n";
        fwrite($handle, $data);
        fclose($handle);
        return(array('client'=> $next_nas['client'],'server' =>'pptpd','secret'=> $password,'ip' => $next_nas['ip']));
    }


    function _show_next_nas(){

        Configure::load('yfi');
        $chap_file  = Configure::read('pptpd.chap_secrets');
        $base_name  = Configure::read('pptpd.yfi_nas_base_name');
        $start_ip   = Configure::read('pptpd.start_ip');

        $next_client;
        $next_ip;

        $content    = file($chap_file);
        $match_line = '';

        foreach($content as $line){

            if(preg_match("/^$base_name/",$line)){

                $line = ltrim($line);
                $match_line = $line;
            }
        }

        if($match_line != ''){

            $pieces     = explode(" ",$match_line);
            $client     = $pieces[0];
            $server     = $pieces[1];
            $secret     = $pieces[2];
            $ip         = $pieces[3];
            
            $next_client    = $this->_get_next_client($base_name,$client);
            $next_ip        = $this->_get_next_ip($ip);

        }else{

            $next_client    = $base_name.sprintf("%04d", 1);
            $next_ip        = $start_ip;
        }
        return(array("client"=>$next_client,"ip" => $next_ip));
    }


    function _get_next_client($base_name,$client){

        $current = preg_replace("/$base_name/",'',$client);
        return $base_name.sprintf("%04d", $current+1);
    }

    function _get_next_ip($ip){

        $pieces     = explode('.',$ip);
        $octet_1    = $pieces[0];
        $octet_2    = $pieces[1];
        $octet_3    = $pieces[2];
        $octet_4    = $pieces[3];

        if($octet_4 >= 254){
            $octet_4 = 1;
            $octet_3 = $octet_3 +1;
        }else{

            $octet_4 = $octet_4 +1;
        }
        $next_ip = $octet_1.'.'.$octet_2.'.'.$octet_3.'.'.$octet_4;
        return $next_ip;
    }

    function _generatePassword ($length = 8){

        // start with a blank password
        $password = "";
        // define possible characters
       // $possible = "!#$%^&*()+=?0123456789bBcCdDfFgGhHjJkmnNpPqQrRstTvwxyz";
        $possible = "0123456789bBcCdDfFgGhHjJkmnNpPqQrRstTvwxyz";
        // set up a counter
        $i = 0; 
        // add random characters to $password until $length is reached
        while ($i < $length) { 

            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
            // we don't want this character if it's already in the password
            if (!strstr($password, $char)) { 
                $password .= $char;
                $i++;
            }
        }
        // done!
        return $password;
    }


}



?>