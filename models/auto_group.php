<?
class AutoGroup extends AppModel {
    var $name       = 'AutoGroup';

     var $hasMany = array(
                    'AutoSetup' => array(
                    'className' => 'AutoSetup',
                    'order' => 'AutoSetup.created DESC'
                    )
        );
}
?>