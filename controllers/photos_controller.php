<?
class PhotosController extends AppController {
    var $name       = 'Photos';
    var $helpers    = array('Javascript');

    var $components = array('Session');    //Add the locker component

    var $scaffold;

}
?>
