<?
class Batch extends AppModel {
    var $name       = 'Batch';
     
    var $hasAndBelongsToMany = array(
             'Voucher' => array('className' => 'Voucher',  
                         'joinTable' => 'batches_vouchers',  
                         'foreignKey' => 'batch_id',  
                         'associationForeignKey' => 'voucher_id',
                         'fields' => array('Voucher.id'), 
                         'unique' => true  
             )
     );

     var $belongsTo = array(
        'Realm' => array(
            'className'     => 'Realm',
            'foreignKey'    => 'realm_id'
        )
    ); 

}
?>