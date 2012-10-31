<?
class LoginPagesController extends AppController {
    var $name       = 'LoginPages';
    var $helpers    = array('Javascript');

    var $components = array('Session','Rights','Json','Dojolayout');    //Add the locker component
    var $uses       = array('Na','Realm','Photo');

    function beforeFilter() {
       $this->Auth->allow('json_login_info','browser_detect');       //Comment out to remove public display of Google Map overlay
    }

    //--A simple detection to determine which login page should be displayed---
    function browser_detect(){

        Configure::load('yfi');
        $directory  = Configure::read('realm.icon_directory');

        App::import('Vendor', 'browser/browser');
        //--Specify the two login pages for the Captive portal--
        $mobile_url     = Configure::read('dynamic_login.mobile')."?".$_SERVER['QUERY_STRING'];
        $standard_url    = Configure::read('dynamic_login.standard')."?".$_SERVER['QUERY_STRING'];

        $browser = new Browser();
        if($browser->isMobile()){
            header("Location: $mobile_url");
        }else{
            header("Location: $standard_url");
        }
    }

    function json_login_info($nas_name){

        $this->layout   = 'ajax';

        $callback       = false;
        if(array_key_exists('callback',$this->params['url'])){
           $callback = $this->params['url']['callback'];
        }

        //Get the primary realm this nas belongs to
        $r = $this->Na->find('first',array('conditions' => array('Na.shortname' => "$nas_name")));
        Configure::load('yfi');
        $directory  = Configure::read('realm.icon_directory');
        //Filter out the /var/www
        $directory = preg_replace("/\/var\/www/","", $directory);
        $directory = preg_replace("/\/usr\/share\/nginx\/www/","", $directory);

        //Get the photo info for the realm
        $p = $this->Realm->find('first',array('conditions' => array('Realm.id' => $r['Realm']['id'])));


        if(count($p['Photo'])>0){
            $count = 0;
            foreach($p['Photo'] as $photo){
                //Update the filename
                $p['Photo'][$count]['file_name'] = $directory.$p['Photo'][$count]['file_name'];
                $count++;
            }
            $json_return['json']['photos']  = $p['Photo'];
        }
        //Add a path for the icon
        $r['Realm']['icon_file_name'] = $directory.$r['Realm']['icon_file_name'];

        $json_return['json']['info']    = $r['Realm'];

        $json_return['json']['status']  = "ok";
        $this->set('json_return',$json_return);
        $this->set('callback',$callback);
    }

}
?>
