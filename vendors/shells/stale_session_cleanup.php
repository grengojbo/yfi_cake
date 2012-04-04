<?php

class StaleSessionCleanupShell extends Shell {
//This script is handy to 'auto-close' stale sessions after there has been no updates from the NAS on active connections for Configure::read('stale_session.close_after'); seconds

    //-----------Call this shell like so:---------------------------------
    //"/var/www/c2/cake/console/cake -app /var/www/c2/yfi_cake stale_session_cleanup";
    // Run it as www-data because of the temp files belonging to www-data
    //---------------------------------------------------------------------

    //--------------------------------------------------------
    //---PREDEFINED VARIABLES---------------------------------
    //--------------------------------------------------------
    //Models that we will be using
   var $uses       = array('Radacct');

     function initialize()
    {
        $this->_loadModels();

        //-----------------------------------------------------------
        //--Load configuration variables ----
        Configure::load('yfi');
        //-----------------------------------------

        $this->stale_after = Configure::read('stale_session.close_after');
        //----------------------------------------
    }       


   function main(){

        print_r("Closing stale sessions after ".$this->stale_after." seconds\n");
        $stale_after = $this->stale_after;
        $this->Radacct->query("UPDATE radacct set acctstoptime=ADDDATE(acctstarttime, INTERVAL acctsessiontime SECOND), acctterminatecause='Clear-Stale-Session' where acctstoptime is NULL AND ((UNIX_TIMESTAMP(now()) - (UNIX_TIMESTAMP(acctstarttime)+acctsessiontime))> $stale_after)");

    }

}

?>
