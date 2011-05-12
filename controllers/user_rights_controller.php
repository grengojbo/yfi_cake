<?
class UserRightsController extends AppController {
    var $name       = 'UserRights';
    var $helpers    = array('Javascript');
    var $uses       = array('UserRight','Group','GroupRight','User','RightCategory');

    var $components = array('Session');    //Add the locker component

    //var $scaffold;

    function json_rights_for($user_id){

        $this->layout = 'ajax';

        //----Get the group the user belongs to -----
        $q_user    = $this->User->find('first',array('recusive' => -1,'conditions' => array('User.id' => $user_id)));

        $group_id  = $q_user['User']['group_id'];
        //--------------------------------------------

        //---------------------------------------------
        //Create a lookup hash for the Right Categories
        $right_categories   = array();
        $r_c = $this->RightCategory->find('all',array('recursive' => -1));
        foreach($r_c as $category){
            $cat_id     = $category['RightCategory']['id'];
            $cat_name   = $category['RightCategory']['name'];
            $right_categories[$cat_id]= $cat_name;
        }


        //---Arrange the rights according to categories----
        $rights = array();

        //Get a list of group rights for this group.
        $q_r    = $this->GroupRight->find('all',array('recursive' => 0 , 'conditions' => array('GroupRight.group_id' => $group_id)));
        foreach($q_r as $item){

            $cat_id     = $item['Right']['right_category_id'];
            $cat_name   = $right_categories[$cat_id];
            if (!array_key_exists($cat_name, $rights)) {
                $rights[$cat_name] = array();
            }
            array_push($rights[$cat_name],array('id' => $item['Right']['id'], 'name' =>$item['Right']['name'], 'description' => $item['Right']['description'], 'state' => $item['GroupRight']['state'] ));
        }

        //Do the personal overrides
        $q_r    = $this->UserRight->find('all',array('recursive' => 0, 'conditions' =>array('UserRight.user_id' => $user_id)));
        foreach($q_r as $item){

            $cat_id     = $item['Right']['right_category_id'];
            $cat_name   = $right_categories[$cat_id];
            $right_id   = $item['Right']['id'];
            #get the group rights for this category
            $group_rights = $rights[$cat_name];
            $counter    = 0;
            foreach($group_rights as $right){
                if($right['id'] == $right_id){
                    $rights[$cat_name][$counter] = array('id' => $item['Right']['id'], 'name' =>$item['Right']['name'], 'description' => $item['Right']['description'], 'state' => $item['UserRight']['state'] );
                }
                $counter++;
            }
        }
        $json_return['json']['status']      = 'ok';
        $json_return['rights']              = $rights;
        $this->set('json_return',$json_return);

    }

    function json_default_group($gridData){

        $this->layout = 'ajax';

        $pieces = explode("_", $gridData);

        $rightsCategory = $pieces[1];
        $userId         = $pieces[2];

        //print("$rightsCategory AND $userId");

        //Get all the rights belonging to this Category
        $q_r = $this->RightCategory->find('first',array('conditions' => array('RightCategory.name' => $rightsCategory)));

        $rights_for_cat = $q_r['Right'];
        foreach($rights_for_cat as $right){
            //Check if user does not perhaps have this right and remove it
            $this->UserRight->deleteAll(array('UserRight.user_id'=> $userId,'UserRight.right_id' => $right['id']));
        }

        $rights = array();
        $counter = 0;

        foreach($rights_for_cat as $right){

            $state = $this->_getStateOfUserRight($userId,$right['id']);
            $rights[$counter]= array('id' => $right['id'], 'name' => $right['name'], 'description' => $right['description'], 'state' => $state );
            $counter++;
        }

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']    = 'ok'; 
        $json_return['label']             = 'name';
        $json_return['identifier']        = 'id';
        $json_return['items']             = $rights;
        //-----------------------------------------

        $this->set('json_return',$json_return);

    }

    function json_toggle_rights($gridData){

        $this->layout = 'ajax';

        $pieces = explode("_", $gridData);
        $rightsCategory = $pieces[1];
        $userId         = $pieces[2];

        $toggled    = array();
        foreach(array_keys($this->params['url']) as $key){
            if(preg_match('/^\d+/',$key)){
                $rightId = $this->params['url'][$key];
                $new_state = $this->_toggle($rightId,$userId);
                array_push($toggled,array('id'=> $rightId, 'state' => $new_state));
            }
        }

        //---Prepare the JSON--------------------
        $json_return = array();
        $json_return['json']['status']      = 'ok'; 
        $json_return['toggled']             = $toggled; 
       
        //-----------------------------------------

        $this->set('json_return',$json_return);
    }


    function _toggle($rightId,$userId){

        $q_r = $this->UserRight->find('first',array('conditions' => array('UserRight.right_id' => $rightId, 'UserRight.user_id' => $userId)));

        if($q_r){

            //get current value
            $current_value = $q_r['UserRight']['state'];
            $new_value = 0;

            if($current_value == 0){
                $new_value = 1;
            }

            $this->UserRight->id = $q_r['UserRight']['id'];
            $this->UserRight->saveField('state', $new_value);
            $this->UserRight->id =false;

            return $new_value;

        }else{

            $d['UserRight']['user_id']      = $userId;
            $d['UserRight']['right_id']     = $rightId;
            $state = $this->_getStateOfUserRight($userId,$rightId);

            if($state == 0){
                $state = 1;
            }else{
                $state = 0;
            }
            $d['UserRight']['state']    = $state;
            $this->UserRight->save($d);
            $this->UserRight->id =false;
            return $state;
        }
    }

    function _getStateOfUserRight($userId,$rightId){

        //Get the Group of the user
        $q_user    = $this->User->findById($userId);
        $groupId   = $q_user['User']['group_id'];

        $state = 0;

        $q_r = $this->GroupRight->find('first',array('conditions' => array('GroupRight.group_id' => $groupId,'GroupRight.right_id' => $rightId)));

        if($q_r){
            $state = $q_r['GroupRight']['state']; 
        }


        $q_usr = $this->UserRight->find('first',array('conditions' => array('UserRight.user_id' => $userId,'UserRight.right_id' => $rightId)));


        if($q_usr){
            $state = $q_usr['UserRight']['state']; 
        }

        return $state;
    }

   

}
?>