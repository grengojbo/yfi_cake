<?php

class Template extends AppModel {

    
     var $hasMany = array(
                    'TemplateAttribute' => array(
                    'className' => 'TemplateAttribute',
                    'order' => 'TemplateAttribute.created DESC'
                    ),
                    'TemplateRealm' => array(
                    'className' => 'TemplateRealm'
                    )
        );
    
}

?>