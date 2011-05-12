<?
class Radcheck extends AppModel {
    var $name       = 'Radcheck';
    var $useTable   = 'radcheck';

    var $hasMany = array(
        'Voucher' => array(
            'className'  => 'Voucher',
            'conditions' => array(),
            'order'      => 'Voucher.created DESC'
        )
    );
}
?>