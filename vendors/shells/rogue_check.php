<?php

class RogueCheckShell extends Shell {

    //-----------Call this shell like so:---------------------------------
    //"/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake rogue_check";
    // Run it as www-data because of the temp files belonging to www-data
    //---------------------------------------------------------------------


   function main(){

         //---Initialization code------------------------------
        App::import('Core', array('View', 'Controller'));
        App::import('Controller', array('AccessPoints'));
        $this->AccessPointsController = new AccessPointsController();
        $this->AccessPointsController->constructClasses();

        //==============END OF SECTION =======================

        if(array_key_exists('end',$this->params)){
            print("===End of month housekeeping check===\n");
          //  $this->AccountsController->month_end_close();
        }
        print("===Starting Rogue AP check===\n");
        $this->AccessPointsController->rogue_check();


    }

}

?>