<?php

try {

    header("Content-type: text/plain");

    //=================
    //First we check if the MAC is a valid MAC
    if(!(isset($_GET['mac']))){
        echo "Page called in a wrong way!";
        return;
    }else{
        $mac_addr = $_GET['mac'];
        //Check if the MAC is in the correct format
        $pattern = '/^([0-9a-fA-F]{2}[-]){5}[0-9a-fA-F]{2}$/i';
        if(preg_match($pattern, $mac_addr)< 1){
            $error = "ERROR: MAC missing or wrong";
            echo "$error";
            return;
        }
    }

    //=====================
    //Basic sanity checks complete, now connect....

    //Find the credentials to connect with
    include_once("/var/www/c2/yfi_cake/config/database.php");
    $dbc    = & new DATABASE_CONFIG();
    $host   = $dbc->default['host'];
    $login  = $dbc->default['login'];
    $pwd    = $dbc->default['password'];
    $db     = $dbc->default['database'];

    $dbh    = new PDO("mysql:host=$host;dbname=$db", $login, $pwd, array(PDO::ATTR_PERSISTENT => true));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Check if any of the NAS devices has this MAC defined as it's community
    $stmt_nas_id        = $dbh->prepare("SELECT nasname FROM nas WHERE community= :mac_addr");
    $stmt_nas_id->bindParam(':mac_addr',$mac_addr);
    $stmt_nas_id->execute();

    $result             = $stmt_nas_id->fetch(PDO::FETCH_ASSOC);
    if($result == ''){
        $return_string = "HEARTBEAT=NO";
    }else{
        $nasname = $result['nasname'];
        $return_string = "HEARTBEAT=$nasname";
    }

    print $return_string;
    $dbh = null;
}
catch(PDOException $e){
    echo $e->getMessage();
    
}

?>
