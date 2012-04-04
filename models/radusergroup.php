<?
class Radusergroup extends AppModel {
    var $name       = 'Radusergroup';
    var $useTable   = 'radusergroup';

     function removeUser($name)
     {
         $ret = $this->query("DELETE FROM radusergroup
                                  WHERE username='$name'"); 
         return $ret;
     }


}
?>