<?
class Note extends AppModel {
    var $name       = 'Note';
    var $belongsTo = array(
        'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                    ),
        'Section' => array(
                    'className' => 'Section',
                    'foreignKey' => 'section_id'
                    )
        );
}
?>