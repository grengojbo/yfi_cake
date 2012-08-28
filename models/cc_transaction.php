<?
class CcTransaction extends AppModel {
    var $name       = 'CcTransaction';

    var $hasMany = array(
                    'TransactionDetail' => array(
                    'className' => 'TransactionDetail',
                    'order' => 'TransactionDetail.name ASC'
                    )
        );
}
?>
