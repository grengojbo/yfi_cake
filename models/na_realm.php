<?php

class NaRealm extends AppModel {

     var $belongsTo = array(
        'Na' => array(
                    'className' => 'Na',
                    'foreignKey' => 'na_id'
                    ),
        'Realm' => array(
                    'className' => 'Realm',
                    'foreignKey' => 'realm_id'
                    ),
        );
}

?>