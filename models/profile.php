<?php

class Profile extends AppModel {

     var $hasMany = array(
                    'ProfileRealm' => array(
                    'className' => 'ProfileRealm'
                    ),
                    'IptableRule' => array(
                        'className' => 'IptableRule'
                    )
        );

     var $belongsTo = array(
        'Template' => array(
                    'className' => 'Template',
                    'foreignKey' => 'template_id'
                    )

        );
}
?>