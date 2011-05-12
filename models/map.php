<?php

class Map extends AppModel {
     var $belongsTo = array(
        'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                    )
        );
}

?>