<?php

class MonthEndShell extends Shell {

    //-----------Call this shell like so:---------------------------------
    //"/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake month_end"; (specify -zero to kick all the permanent users connected off and zero their percentage if applicable)
    // Run it as www-data because of the temp files belonging to www-data
    //---------------------------------------------------------------------


    //==========================================================================
    //--We run this script at 23:50 each day as well as 00:01 with the -zero switch----
    //--The following switches exists for this shell:------------------------
    // -start      => This kicks off ALL ACTIVE permanent users and zero ALL permanent users pecentage (NOT PREPAID Type) if it is the start of a new month cycle if not exit
    // -end        => Just kick off ALL ACTIVE permanent users if it is the end of a month - run to close our account records at the end of the month
    //==========================================================================


   function main(){

         //---Initialization code------------------------------
        App::import('Core', array('View', 'Controller'));
        App::import('Controller', array('Accounts'));
        $this->AccountsController = new AccountsController();
        $this->AccountsController->constructClasses();

        //==============END OF SECTION =======================

        if(array_key_exists('end',$this->params)){
            print("===End of month housekeeping check===\n");
            $this->AccountsController->month_end_close();
        }

        if(array_key_exists('start',$this->params)){
            print("===Start of month housekeeping check===\n");
            $this->AccountsController->month_start_reset();
        }

    }

}

?>