<?
class GroupRight extends AppModel {
    var $name = 'GroupRight';

    var $belongsTo = array(
        'Group' => array(
                    'className' => 'Group',
                    'foreignKey' => 'group_id'
                    ),
        'Right' => array(
                    'className' => 'Right',
                    'foreignKey' => 'right_id'
                    )
        ); 

}
?>