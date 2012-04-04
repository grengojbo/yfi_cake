<?
class Credit extends AppModel {
    var $name       = 'Credit';

    var $belongsTo = array(
        'User' => array(
            'className'    => 'User',
            'foreignKey'    => 'user_id'
        ),
        'Realm' => array(
            'className'     => 'Realm',
            'foreignKey'    => 'realm_id'
        ),
        'UsedBy' => array(
            'className'     => 'User',
            'foreignKey'    => 'used_by_id'
        )

    );
     

}
?>