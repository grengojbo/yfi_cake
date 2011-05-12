<?php

class UserRealm extends AppModel {

    
     var $belongsTo = array(
        'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                    ),
        'Realm' => array(
                    'className' => 'Realm',
                    'foreignKey' => 'realm_id'
                    ),
        ); 
    
}

?>