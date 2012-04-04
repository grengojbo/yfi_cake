<?
class LanguagesController extends AppController {
    var $name       = 'Languages';
    var $scaffold;

    /*
        if($this->Session->check('LandingPages.language')){

            $language = $this->Session->read('LandingPages.language');
        }else{

            //----------START OF CONFIG -----------------------------------
            //Get the configuration values from the configuration table
            $lang       = $this->ConfigurationItem->find("name='language'");
            $language   = $lang['ConfigurationItem']['value'];
            //$locale_location = "/var/www/cake/hotcakes/plugins/locale";
            //----------END OF CONFIG ---------------------------------------
        }
        putenv("LANG=$language"); 
        putenv("LANGUAGE=$language"); 
        setlocale(LC_ALL, $language);
    */

}
?>