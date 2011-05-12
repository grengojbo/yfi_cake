<?php

class RightCategory extends AppModel {

     var $name = 'RightCategory';

     var $hasMany = array(
            'Right' => array(
            'className' => 'Right',
            'foreignKey' => 'right_category_id',
            'dependent'=> true
        )
    ); 
}

?>