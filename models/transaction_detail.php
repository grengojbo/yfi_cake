<?php

class TransactionDetail extends AppModel {
     var $belongsTo = array(
        'CcTransaction' => array(
                    'className' => 'CcTransaction',
                    'foreignKey' => 'cc_transaction_id'
                    )
        );
}

?>
