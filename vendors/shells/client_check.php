<?php

class ClientCheckShell extends Shell {

    //-----------Call this shell like so:---------------------------------
    //"/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake client_check";
    // Run it as www-data because of the temp files belonging to www-data
    //---------------------------------------------------------------------


   function main(){

         //---Initialization code------------------------------
        App::import('Core', array('View', 'Controller'));
        App::import('Controller', array('AccessPoints'));
        $this->AccessPointsController = new AccessPointsController();
        $this->AccessPointsController->constructClasses();

        //==============END OF SECTION =======================
        print("===Starting Rogue AP check===\n");
        $this->AccessPointsController->client_check();
    }

}

?>