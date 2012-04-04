<?php

class RogueAp extends AppModel {
     var $belongsTo = array(
        'Na' => array(
                    'className' => 'Na',
                    'foreignKey' => 'na_id'
                    )
        );
}

?>