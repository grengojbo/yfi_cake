<?
class NotificationDetail extends AppModel {
    var $name       = 'NotificationDetail';
    var $useTable   = 'notification_details';

    var $belongsTo = array(
        'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                    )
        );

}
?>