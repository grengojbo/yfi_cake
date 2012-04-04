<?
class User extends AppModel {
    var $name = 'User';

    var $belongsTo = array(
        'Group' => array(
                    'className' => 'Group',
                    'foreignKey' => 'group_id'
                    ),
        'Profile' => array(
            'className'     => 'Profile',
            'foreignKey'    => 'profile_id'
        ),
        'Creator' => array(
            'className'    => 'User',
            'foreignKey'    => 'user_id'
        ),
        'Radcheck' => array(
            'className'    => 'Radcheck',
            'foreignKey'    => 'radcheck_id',
            'conditions'    => array('Attribute' => 'Cleartext-Password')
        ),
        'Realm' => array(
            'className'     => 'Realm',
            'foreignKey'    => 'realm_id'
        ),
        'Language' => array(
            'className'     => 'Language',
            'foreignKey'    => 'language_id'
        )
    );

         var $hasMany = array(
                    'UserRight' => array(
                        'className' => 'UserRight',
                        'order' => 'UserRight.created DESC'
                    ),
                   'UserRealm' => array(
                    'className' => 'UserRealm',
                    'order' => 'UserRealm.created DESC'
                    ),
                    'Invoice' =>array(
                        'className' => 'Invoice',
                        'order' => 'Invoice.start_date DESC'
                    ),
                    'Payment' => array(
                        'className' => 'Payment',
                        'order' => 'Payment.created DESC'
                    ),
                    'Device' => array(
                        'className' => 'Device',
                        'order' => 'Device.created DESC'
                    ),
                    'Map' => array(
                        'className' => 'Map',
                        'order' => 'Map.created DESC'
                    )
        );
        

}
?>