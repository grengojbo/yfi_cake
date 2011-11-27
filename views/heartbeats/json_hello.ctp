<?php
    Configure::write('debug', 0);

    if(isset($error)){
        echo $error;
    }else{

        echo $return_string;
    }
?>
