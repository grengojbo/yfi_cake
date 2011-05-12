<?php

class BillingPlanRealm extends AppModel {

     var $belongsTo = array(
        'BillingPlan' => array(
                    'className'     => 'BillingPlan',
                    'foreignKey'    => 'billing_plan_id'
                    ),
        'Realm' => array(
                    'className'     => 'Realm',
                    'foreignKey' => 'realm_id'
                    ),
        );
}

?>