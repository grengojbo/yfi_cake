<?
class AppController extends Controller {
    /**
     * components
     * 
     * Array of components to load for every controller in the application
     * 
     * @var $components array
     * @access public
     */
    var $components = array('Session','Auth');
    var $helpers    = array('Session','Html','Form');

    var $uses = array('User','Group','GroupRight','RightCategory','UserRight','UserRealm','Language');

    function beforeFilter() {


        //____ Dummy TEST_________

        /*
        putenv("LANG=af_ZA.utf8"); 
        putenv("LANGUAGE=af_ZA.utf8"); 
        setlocale(LC_ALL, 'af_ZA.utf8');
        // Set the text domain as 'messages'
        $domain = 'messages';
        bindtextdomain($domain,'/var/www/c2/yfi_cake/plugins/locale' ); 
        textdomain($domain);
        */
        //________________________

        //-----------------------------------------------------------
        //--Load configuration variables ----
        Configure::load('yfi'); //Load the config file which contains application wide settings / values
        //-----------------------------------------------------------

        if($this->Auth->user()){

            //Check if language has been set
            if(!$this->Session->check('LanguageIsoName')){  //We only do this lookup once to save calls to the DB
                $this->_set_language_name();
            }

            $iso_name           = $this->Session->read('LanguageIsoName');
            $locale_location    = Configure::read('locale.location');
            putenv("LANG=$iso_name.utf8"); 
            putenv("LANGUAGE=$iso_name.utf8");
            
           // setlocale(LC_ALL, "$iso_name.utf8");
            setlocale(LC_MESSAGES,  "$iso_name.utf8");
            setlocale(LC_CTYPE,     "$iso_name.utf8");
            setlocale(LC_TIME,      "$iso_name.utf8");
            setlocale(LC_NUMERIC,   "en");
            // Set the text domain as 'messages'
            $domain = 'messages';
            bindtextdomain($domain,$locale_location ); 
            textdomain($domain);


            //We are logged in, but is the AuthInfo populated already?
            if(!$this->Session->check('AuthInfo')){
                $this->_populate_authinfo();
            }
        }
    }


    function beforeRender(){

    }

    function _set_language_name(){
        $user           = $this->Auth->user();
        $language_id    = $user['User']['language_id'];
        $qr             = $this->Language->findById($language_id);
        $iso_name       = $qr['Language']['iso_name'];
        $iso_name = $this->Session->write('LanguageIsoName',$iso_name);
    }

    function _populate_authinfo(){
    //-------------Populate the Environment variable with the rights that the user has --------
    
        $auth_info  = array();
        $auth_info  = $this->Auth->user();

        //-------Group--------------------------
        $q_r        = $this->Group->find('first', array('conditions' => array('Group.id' => $auth_info['User']['group_id'])));
        $auth_info['Group']  = $q_r['Group'];
        //--------------------------------------

        //------Group Rights-----------------------
        //Get all the group rights
        $q_r = $this->GroupRight->findAllByGroupId($auth_info['Group']['id']);
        foreach($q_r as $item){
            $right          = $item['Right']['name'];
            $description    = $item['Right']['description'];
            $rc_id          = $item['Right']['right_category_id'];
            $category       = $this->_get_right_category($rc_id);
            $state          = $item['GroupRight']['state'];
            $type           = 'group';
            $auth_info['Rights'][$right] = array('type' => $type, 'state' => $state, 'category' => $category, 'description' => $description); 
        }
        //----------------------------------------------------


        //----------User Rights---------------------------------------------
        $q_r = $this->UserRight->findAllByUserId($auth_info['User']['id']);  //This will override the group rights
        foreach($q_r as $item){
            $right          = $item['Right']['name'];
            $description    = $item['Right']['description'];
            $rc_id          = $item['Right']['right_category_id'];
            $category       = $this->_get_right_category($rc_id);
            $state          = $item['UserRight']['state'];
            $type           = 'user';
            $auth_info['Rights'][$right] = array('type' => $type, 'state' => $state, 'category' => $category, 'description' => $description); 
        }
        //----------------------------------------------------------------------


        //---------------User' Realms ---------------------------------------
        $q_r = $this->UserRealm->findAllByUserId($auth_info['User']['id']);
        $auth_info['Realms'] = array();
        foreach($q_r as $item){
            array_push($auth_info['Realms'], $item['Realm']);
        }
        //-------------------------------------------------------------------

       // print_r($auth_info);
        $this->Session->write('AuthInfo',$auth_info);
 
    //---------------------------------------------------
    }

    function _get_right_category($rc_id){
        $q_r    = $this->RightCategory->findById($rc_id);
        return $q_r['RightCategory']['name'];
    }
}

?>
