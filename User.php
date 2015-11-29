<?php

namespace PetakUmpet;

use PetakUmpet\Database\Accessor;
use PetakUmpet\Database\Model;
use PetakUmpet\Security\Password;

class User {

  private $data;

  private $parentId;
  private $isAdmin;
  private $userTable;
  private $userProfile;

  // shortcuts values
  private $id;
  private $name;
  private $userid;
  private $profile;
  private $confirmedAt;
  private $accessQuery;

  private function getPropAndDestroy($arr, $prop) 
  {
    $ret = null;
    if (isset($arr) && isset($arr[$prop])) {
      $ret = $arr[$prop];
      unset($arr[$prop]);
    }
    return $ret;
  }

  public function __construct($username, $password, $userTable = 'userdata', $userProfile='user_profile')
  {
    $this->userTable = $userTable;
    $this->userProfile = $userProfile;

    $dba = new Accessor($this->userTable);
    $userdata = $dba->findOneBy(array('userid' => $username));
    
    $upassword = $this->getPropAndDestroy($userdata, 'password');
    
    if (!Password::check($password, $upassword)) {
      $userdata = null; 
    }
    $upassword = $password = null ;

    $this->data = $userdata;
    $this->accessQuery = null;
    $this->name = null;
    $this->id = null;
    $this->userid = null;
    $this->firstLogin = true;
    $this->profile = array();
    $this->roles = array();
    $this->access = array();
    $this->parentId = null;
    $this->isAdmin = false;
    $this->confirmedAt = false;
  }

  public function getName()   { return $this->name;   }
  public function getUserid() { return $this->userid; }
  public function getId() { return $this->id; }
  public function getData()   { return $this->data;   }
  public function isFirstLogin()   { return $this->firstLogin;   }
  public function getConfirmedAt() { return $this->confirmedAt; }
  public function isConfirmed() { return $this->confirmedAt !== null; }

  public function setFirstLogin($value)
  {
    $this->first_login = $value;
    $data['id'] = $this->id;
    $data['first_login'] = $value;
    $m = new Model($this->userTable);
    $m->save($data);
  }
  
  public function getProfileData($key) 
  { 
    if (is_array($this->profile) && isset($this->profile[$key])) {
      return $this->profile[$key];
    } 
    return false;
  }

  public function setProfileData($key, $value)
  {
    $this->profile[$key] = $value;
    $dba = new Model($this->userProfile);
    $dba->save($this->profile, array('user_id'));
  }

  public function validate()
  {
    if ($this->data && $this->data !== null) {
      $this->name = $this->data['name'];
      $this->userid = $this->data['userid'];
      $this->id = $this->data['id'];
      $this->firstLogin = $this->data['first_login'];
      $this->confirmedAt = $this->data['confirmed_at'];
      $this->isAdmin = $this->data['is_admin'];
      $dba = new Accessor($this->userProfile);
      $this->profile = $dba->findOneBy(array('user_id' => $this->id));
      return true;
    }
    return false;
  }
  
  public function setAccessQuery($query)
  {
    $this->accessQuery = $query;
  }

  public function hasAccess($page, $refresh=false)
  {
    if ($this->isAdmin) return true;
    $db = Singleton::acquire('\\PetakUmpet\\Database');

    $query = $this->accessQuery;
    if ($query === null) {
      $query = "SELECT a.name FROM userdata u "
            . "JOIN user_role ur ON u.id = ur.user_id "
            . "JOIN role_access ra ON ra.role_id = ur.role_id "
            . "JOIN accessdata a ON ra.access_id = a.id "
            . "WHERE u.userid = ?" ;
    }

    $access = array();
    $status = false;

    $rows = $db->queryFetchAll($query, array($this->userid));

    if ($rows) {
      foreach ($rows as $r) {
        $access[$r['name']] = true;
        if ($page == $r['name']) {
          $status = true;
        }
      }
    }
    return $status;
  }

}