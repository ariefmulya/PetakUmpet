<?php

namespace PetakUmpet;

use PetakUmpet\Database\Accessor;


class User {

  private $data;
  private $roles;
  private $access;

  private $parentId;
  private $isAdmin;

  // shortcuts values
  private $name;
  private $userid;

  public function __construct($username, $password)
  {
    $dba = new Accessor('userdata');
    $userdata = $dba->findOneBy(array('userid' => $username, 'password' => $password));

    $this->data = $userdata;
    $this->name = null;
    $this->userid = null;
    $this->roles = array();
    $this->access = array();
    $this->parentId = null;
    $this->isAdmin = false;
  }

  public function __call($name, $args)
  {
    if (substr($name, 0,3) == 'get') {
      if (isset($this->data[strtolower(substr($name, 3))])) {
        return $this->data[strtolower(substr($name, 3))];
      }
    }
    return null;
  }

  public function validate()
  {
    if ($this->data !== null) {
      $this->name = $this->data['name'];
      $this->userid = $this->data['userid'];
      return true;
    }
    return false;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getUserid()
  {
    return $this->userid;
  }

  public function hasAccess($page, $refresh=false)
  {
    if (count($this->access) > 0 && !$refresh) {
      if (isset($this->access[$page]) && $this->access[$page] === true) {
        return true;
      }
      return false;
    } 

    $query = "SELECT a.name FROM userdata u "
            . "JOIN user_role ur ON u.id = ur.user_id "
            . "JOIN role_access ra ON ra.role_id = ur.role_id "
            . "JOIN accessdata a ON ra.access_id = a.id "
            . "WHERE u.userid = ?" ;

    $access = array();
    $status = false;

    $db = Singleton::acquire('\\PetakUmpet\\Database');

    $rows = $db->QueryFetchAll($query, array($this->userid));

    if ($rows) {
      foreach ($rows as $r) {
        $access[$r['name']] = true;
        if ($page == $r['name']) $status = true;
      }
    }
    $this->access = $access;
    return $status;
  }

}