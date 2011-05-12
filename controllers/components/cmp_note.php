<?php

class CmpNoteComponent extends Object {

    //----Component with common functions related to Notes for user ----------------------------------------------
    //---NOTE: This idea came in late - so not all controllers use it, newer controllers will make use of it -----
    //---This was to allow easy third party integrations ---------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------
    //---The controller who uses the component needs to use the following models:

    var $components = array('Session','Rights','Formatter');

    function initialize(&$controller) {
        // saving the controller reference for later use
        $this->controller =& $controller;
        $this->controller->loadModel('Note');
        $this->controller->loadModel('Section');
    }

    function addNote($note_detail){

        $q_r = $this->controller->Section->find('first',array('conditions' => array('Section.name' => $note_detail['section_name'])));
        $section_id = $q_r['Section']['id'];

        $d['Note']['user_id']       = $note_detail['user_id'];
        $d['Note']['section_id']    = $section_id;
        $d['Note']['value']         = $note_detail['value'];
        $this->controller->Note->save($d);
        $this->controller->Note->id  =false;    //Clear the ID 
    }

}

?>
