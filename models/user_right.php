<?php

class UserRight extends AppModel {

     var $belongsTo = array(
        'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                    ),
        'Right' => array(
                    'className' => 'Right',
                    'foreignKey' => 'right_id'
                    ),
        ); 

}

?>