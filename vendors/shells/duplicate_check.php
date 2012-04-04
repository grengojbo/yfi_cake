<?php

class DuplicateCheckShell extends Shell {

    //-----------Call this shell like so:---------------------------------
    //"/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake duplicate_check";
    // Run it as www-data because of the temp files belonging to www-data
    //---------------------------------------------------------------------

    //--Script which will remove duplicate radacct entries

   function main(){

        Configure::write('debug', 0);

         //---Initialization code------------------------------
        App::import('Core', array('View', 'Controller'));
        App::import('Controller', array('Radaccts'));
        $this->RadacctsController = new RadacctsController();
        $this->RadacctsController->constructClasses();
        //==============END OF SECTION =======================

        print("===Starting Duplicates check===\n");
        $this->RadacctsController->duplicate_check();
    }

}

?>
