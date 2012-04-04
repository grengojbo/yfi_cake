<?php
class JsonLayoutsController extends AppController {

    var $name       = 'JsonLayouts';
    var $helpers    = array('Html', 'Form','Javascript' );
    var $components = array('Rights','Dojolayout');
    var $uses       = array('Realms');

    function left_pane(){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return                        = array();
        $json_return['json']['status']      = 'ok'; 
        $json_return['menu']                = $this->Dojolayout->left_pane();
        //---------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }

    function workspace(){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return                        = array();
        $json_return['json']['status']      = 'ok'; 
        $json_return['menu']                = $this->Dojolayout->workspace();
        //---------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------
    }

    function actions_for_templates(){

        $this->layout = 'ajax';

        //---Prepare the JSON--------------------
        $json_return                        = array();
        $json_return['json']['status']      = 'ok'; 
        $json_return['menu']                = $this->Dojolayout->actions_for_templates();
        //---------------------------------------

        //--Return the JSON --------------------
        $this->set('json_return',$json_return);
        //--------------------------------------

    }


}
?>