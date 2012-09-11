<?
class ExpiryChange extends AppModel {
    var $name       = 'ExpiryChange';

    var $belongsTo = array(
        'CcTransaction' => array(
                    'className' => 'CcTransaction',
                    'foreignKey' => 'cc_transaction_id'
                    ),
        'Initiator'  => array(
                    'className'     => 'User',
                    'foreignKey'    => 'initiator_id'
                    ),
        'User'     => array(
                    'className'     => 'User',
                    'foreignKey'    => 'user_id'
                    )
    );
}
?>
