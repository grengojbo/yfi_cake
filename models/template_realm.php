<?php

class TemplateRealm extends AppModel {

    
     var $belongsTo = array(
        'Template' => array(
                    'className' => 'Template',
                    'foreignKey' => 'template_id'
                    ),
        'Realm' => array(
                    'className' => 'Realm',
                    'foreignKey' => 'realm_id'
                    ),
        ); 
    
}

?>