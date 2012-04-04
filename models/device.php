<?php

class Device extends AppModel {
     var $belongsTo = array(
        'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                    )
        );
}

?>