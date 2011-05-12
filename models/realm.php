<?php

class Realm extends AppModel {

    
     var $hasMany = array(
                    'UserRealm' => array(
                    'className' => 'UserRealm',
                    'order' => 'UserRealm.created DESC'
                    )
        );
    
}

?>