<?php

class ProfileRealm extends AppModel {

    
     var $belongsTo = array(
        'Profile' => array(
                    'className' => 'Profile',
                    'foreignKey' => 'profile_id'
                    ),
        'Realm' => array(
                    'className' => 'Realm',
                    'foreignKey' => 'realm_id'
                    ),
        ); 
    
}

?>