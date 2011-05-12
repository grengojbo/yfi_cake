<?php
class RightsComponent extends Object {

   //This component will determine if a user logged in has the right to execute a certain action in the controller
    var $components = array('Session');    

    function initialize(&$controller) {
        // saving the controller reference for later use
        $this->controller =& $controller;
    }

    function CheckRights(){

        //If the user is part of the administrators group - they are allowd to do anything
        $auth_data = $this->Session->read('AuthInfo');



        Configure::load('yfi');

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            return true;
        }

        if($auth_data['Group']['name'] == Configure::read('group.user')){
            return false;
        }

        $controller = $this->controller->params['controller'];
        $action     = $this->controller->params['action'];
        $perm       = $controller.'/'.$action;

        //print("Check rigth for $perm");

        if(array_key_exists($perm,$auth_data['Rights'])){

            if($auth_data['Rights'][$perm]['state'] == '1'){
                return true;
            }
            return false;
        }
        return false;   //Default
    }

    function GetRealmClause(){
    //--------------------------------------------------------
    //--Use to create an or filter for the Access Providers---
    //--Administrators will be empty to allow to show ALL-----
    //--------------------------------------------------------
        $conditions = array();

        Configure::load('yfi');

        $auth_data = $this->Session->read('AuthInfo');

        //--Realms only need to be checked only for Access Providers--
        if($auth_data['Group']['name'] == Configure::read('group.ap')){   //They can only see whet the are permitted to see 
            if(!empty($auth_data['Realms'])){
                $realm_filter = array();
                foreach($auth_data['Realms'] as $realm_line){
                    $name = $realm_line['name'];
                    array_push($realm_filter,array("Realm.name " => $name));
                }
            }
            array_push($conditions,array('or' => $realm_filter));
        }
        //--------------------------------------------------------
        return $conditions;
    }

     function CheckRealmIdAllowed($realm_id){
    //Determines wheter a realm is assigned to an ap/ admins alway true the rest false

        $return_value = false;
        $auth_data = $this->Session->read('AuthInfo');
        Configure::load('yfi'); //Load the config file

        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            $return_value = true;
        }

        if($auth_data['Group']['name'] == Configure::read('group.ap')){
            foreach($auth_data['Realms'] as $realm){    //Loop the Realms

                if($realm_id == $realm['id']){          //If assigned to AP return true

                    $return_value = true;
                }
            } 
        }
        return $return_value;
    }

    function LookForRight($right){

        $auth_data = $this->Session->read('AuthInfo');

        //Admin users should have every right by default
        if($auth_data['Group']['name'] == Configure::read('group.admin')){
            return true;
        }

        if(array_key_exists('Rights',$auth_data)){
            if(array_key_exists($right,$auth_data['Rights'])){
            
                if($auth_data['Rights'][$right]['state'] == '1'){
                    return true;
                }
                return false;
            }
        }
        return false;   //Default
    }


    /*
    function CheckRealmIdAllowed($id){

        $conditions = array();

        $auth_data = $this->Session->read('AuthInfo');

        if($auth_data['Group']['name'] == 'Access Providers'){   //They can only see whet the are permitted to see 
            if(!empty($auth_data['Realms'])){
                foreach($auth_data['Realms'] as $realm_line){
                    $current_id = $realm_line['id'];
                    if($id == $current_id){ //It is present
                        return true;
                    }
                }
            }
            return false;    //Not in list of Realms asigned to user
        }
        return true;        //Assume Administrators
    }
    */
}


/*
Array
(
    [User] => Array
        (
            [id] => 499ef480-3e84-4725-a720-190aa509ff00
            [username] => pviljoen
            [name] => Piet
            [surname] => viljoen
            [adress] => 100 Klip street Lynwood
            [phone] => 072-000-0000
            [cell] => 072-000-0000
            [fax] => 072-000-0000
            [active] => 1
            [group_id] => 499ef44e-42e8-4615-8d51-2f51a509ff00
            [created] => 2009-02-20 20:20:48
            [modified] => 2009-02-20 20:49:00
        )

    [Group] => Array
        (
            [id] => 499ef44e-42e8-4615-8d51-2f51a509ff00
            [name] => Administrators
            [created] => 2009-02-20 20:19:58
            [modified] => 2009-02-20 20:19:58
        )

    [Rights] => Array
        (
            [/realms/json_index] => Array
                (
                    [type] => user
                    [state] => 0
                    [category] => Realms
                    [description] => List Realms
                )

        )

    [Realms] => Array
        (
            [0] => Array
                (
                    [id] => 499ef36f-4708-4246-b996-190ba509ff00
                    [name] => Stellenbosh
                    [append_string_to_user] => stel
                    [icon_file_name] => logo.jpg
                    [phone] => 072-000-0000
                    [fax] => 072-000-0000
                    [cell] => 072-000-0000
                    [email] => d@yfi.co.za
                    [url] => http://watwat
                    [address] => koos
                    [created] => 2009-02-20 20:16:15
                    [modified] => 2009-02-20 20:16:15
                )

        )

)

*/



?>
