<?php

class JsonComponent extends Object {

    function permAuthListEmpty($label='name',$id='id') {

        $json_return = array();
        $json_return['json']['status']  = 'perm'; 
        $json_return['label']           = 'name';
        $json_return['identifier']      = 'id';
        $json_return['numRows']         = 0;        //Required for Query Stores
        $json_return['items']           = array();
        return $json_return;

    }

    function permFail($label='name',$id='id') {

        $json_return = array();
        $json_return['json']['status']  = 'perm'; 
        return $json_return;

    }

}



?>