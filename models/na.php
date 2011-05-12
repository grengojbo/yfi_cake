<?php

class Na extends AppModel {

     var $hasMany = array(
                    'NaRealm' => array(
                    'className' => 'NaRealm'
                    ),
                    'NaState'   => array(
                        'limit'     => 1,
                        'className' => 'NaState',
                        'order'     => 'NaState.created DESC'
                    ),
                    'RogueAp'     => array(
                        'className' => 'RogueAp',
                    ),
                    'WirelessClient' => array(
                        'className' => 'WirelessClient',
                        'conditions' => array('WirelessClient.active' => 'yes'),
                    )
        );

     var $belongsTo = array(
        'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                    )
       
        );
}

?>
