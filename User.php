<?php

namespace PetakUmpet;

use PetakUmpet\Database\Accessor;
use PetakUmpet\Database\Model;


class User {

  private $data;

  private $parentId;
  private $isAdmin;

  // shortcuts values
  private $id;
  private $name;
  private $userid;
  private $profile;

  public function __construct($username, $password)
  {
    $dba = new Accessor('userdata');
    $userdata = $dba->findOneBy(array('userid' => $username, 'password' => $password));
    unset($userdata['password']);

    $this->data = $userdata;
    $this->name = null;
    $this->id = null;
    $this->userid = null;
    $this->first_login = true;
    $this->profile = array();
    $this->roles = array();
    $this->access = array();
    $this->parentId = null;
    $this->isAdmin = false;
  }

  public function getName()   { return $this->name;   }
  public function getUserid() { return $this->userid; }
  public function getId() { return $this->id; }
  public function getData()   { return $this->data;   }
  public function isFirstLogin()   { return $this->first_login;   }

  public function setFirstLogin($value)
  {
    $this->first_login = $value;
    $data['id'] = $this->id;
    $data['first_login'] = $value;
    $m = new Model('userdata');
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
    $dba = new Model('user_profile');
    $dba->save($this->profile, array('user_id'));
  }

  public function validate()
  {
    if ($this->data && $this->data !== null) {
      $this->name = $this->data['name'];
      $this->userid = $this->data['userid'];
      $this->id = $this->data['id'];
      $this->first_login = $this->data['first_login'];
      $dba = new Accessor('user_profile');
      $this->profile = $dba->findOneBy(array('user_id' => $this->id));
      return true;
    }
    return false;
  }
  
  public function hasAccess($page, $refresh=false)
  {
    $query = "SELECT a.name FROM userdata u "
            . "JOIN user_role ur ON u.id = ur.user_id "
            . "JOIN role_access ra ON ra.role_id = ur.role_id "
            . "JOIN accessdata a ON ra.access_id = a.id "
            . "WHERE u.userid = ?" ;

    $access = array();
    $status = false;

    $db = Singleton::acquire('\\PetakUmpet\\Database');

    $rows = $db->queryFetchAll($query, array($this->userid));

    if ($rows) {
      foreach ($rows as $r) {
        $access[$r['name']] = true;
        if ($page == $r['name']) $status = true;
      }
    }
    return $status;
  }

}