<?php

class NotifyCheckerShell extends Shell {

    //-----------Call this shell like so:---------------------------------
    //"/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake notify_checker";
    //---------------------------------------------------------------------

    //=====================================================================
    //This script is called by cron every x minute(s) It will check the new total usage for permanent users
    //and compare to see if the user were already notified if not they will be notified
    //models defines in the $uses array ('User','Check' and 'Notify' for now)
    //=====================================================================


    function main(){

         //---Initialization code------------------------------
        App::import('Core', array('View', 'Controller'));
        App::import('Controller', array('Notifications'));
        $this->NotificationsController = new NotificationsController();
        $this->NotificationsController->constructClasses();

        //==============END OF SECTION =======================

        $this->NotificationsController->check_notification();
        exit;
    }

}

?>