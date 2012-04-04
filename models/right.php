<?php

class Right extends AppModel {

    var $belongsTo = array(
        'RightCategory' => array(
                    'className'     => 'RightCategory',
                    'foreignKey'    => 'right_category_id'
                    )
        );

     var $hasMany = array(
                    'UserRight' => array(
                    'className' => 'UserRight',
                    'order' => 'UserRight.created DESC'
                    ),
                    'GroupRight' => array(
                    'className' => 'GroupRight',
                    'order' => 'GroupRight.created DESC'
                    )
        );
}

?>