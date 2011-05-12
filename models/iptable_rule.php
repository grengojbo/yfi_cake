<?php

class IptableRule extends AppModel {

     var $belongsTo = array(
        'Profile' => array(
                    'className' => 'Profile',
                    'foreignKey' => 'profile_id'
                    )

        );
}
?>