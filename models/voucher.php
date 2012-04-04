<?
class Voucher extends AppModel {
    var $name       = 'Voucher';

    /*
    var $hasAndBelongsToMany = array(
             'Batch' => array('className' => 'Batch',  
                         'joinTable' => 'batches_vouchers',  
                         'foreignKey' => 'voucher_id',  
                         'associationForeignKey' => 'batch_id',
                         'fields' => array('Batch.id','Batch.name'), 
                         'unique' => true  
             ),
     );
    */
    
    var $belongsTo = array(
        'Profile' => array(
            'className'     => 'Profile',
            'foreignKey'    => 'profile_id'
        ),
        'User' => array(
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
        )

    );
     

}
?>