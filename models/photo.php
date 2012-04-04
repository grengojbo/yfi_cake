<?
class Photo extends AppModel {
    var $name       = 'Photo';

    var $belongsTo = array(
        'Realm' => array(
                    'className' => 'Realm',
                    'foreignKey' => 'realm_id'
                    )
        
        );
}
?>
