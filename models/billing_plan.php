<?php

class BillingPlan extends AppModel {

    var $hasMany = array(
                    'BillingPlanRealm' => array(
                    'className' => 'BillingPlanRealm'
                    )
        );

}

?>