<?
class AutoMacsController extends AppController {
    var $name       = 'AutoMacs';
    var $helpers    = array('Javascript');
    var $components = array('Session');    //Add the locker component

    var $scaffold;

}
?>