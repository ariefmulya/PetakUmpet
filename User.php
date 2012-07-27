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
    $userdata = $dba->findOneBy(array('name' => $username, 'password' => $password));

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

  public function getRoles()
  {
    if (count($this->roles) <= 0) {
      $db = Singleton::acquire('\\PetakUmpet\\Database');

      $query = "SELECT name FROM groupdata g "
              . "JOIN user_group ug ON g.id = ug.group_id "
              . "JOIN userdata u ON ug.user_id = u.id "
              . "WHERE u.id = ? ";

      $roles = $db->QueryAndFetch($query, array($this->userid));

      if (!$roles) {
        return null;
      }
      $this->roles = $roles;
    }

    return $this->roles;
  }


}