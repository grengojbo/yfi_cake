<?php
   Configure::write('debug', 0);
    if($callback != false){
        echo $callback.'('.json_encode($json_return).')';
    }else{
        echo json_encode($json_return);
    }
?>
