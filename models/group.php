<?
class Group extends AppModel {
    var $name = 'Group';

    var $hasMany = array(

                    'User' => array(
                    'className' => 'User',
                    'order' => 'User.created DESC'
                    ),
                 'GroupRight' => array(
                    'className' => 'GroupRight',
                    'order' => 'GroupRight.created DESC'
                    ),
        );

    

}
?>