<?
class AutoSetup extends AppModel {
    var $name       = 'AutoSetup';

     var $belongsTo = array(
        'AutoGroup' => array(
            'className'     => 'AutoGroup',
            'foreignKey'    => 'auto_group_id'
        ),
        'AutoMac' => array(
            'className'    => 'AutoMac',
            'foreignKey'    => 'auto_mac_id'
        )
    );
}
?>