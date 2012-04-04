<?
class AutoMac extends AppModel {
    var $name       = 'AutoMac';

    var $hasMany = array(
                    'AutoSetup' => array(
                    'className' => 'AutoSetup',
                    'order' => 'AutoSetup.created DESC'
                    ),
                    'AutoContact' => array(
                        'className' => 'AutoContact',
                        'order'     => 'AutoContact.created DESC'
                    )
        );


}
?>