<?
class Extra extends AppModel {
    var $name = 'Extra';

    var $belongsTo = array(
        'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                    )
       
        );

    

}
?>