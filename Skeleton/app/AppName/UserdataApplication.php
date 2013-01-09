<?php

namespace frontend;

use PetakUmpet\Application\xCrudApplication;
use PetakUmpet\Database\Accessor;
use PetakUmpet\Response;
use PetakUmpet\Form\Component\TableAdapterForm;

class UserdataApplication extends xCrudApplication {

  public function __construct(\PetakUmpet\Process $process, \PetakUmpet\Request $request, \PetakUmpet\Session $session, \PetakUmpet\Config $config)
  {
    parent::__construct($process, $request, $session, $config);

    $this->tableName = 'userdata';
    $this->columns = array('id', 'name', 'is_admin');

    $this->inlineForm = false;

    // parameter names in queries should be the same with relKey and relatedData keys in tabs array
    $rolesQuery = "SELECT ur.id, r.name FROM user_role ur "
        ."JOIN roledata r ON r.id = ur.role_id "
        ."JOIN userdata u ON u.id = ur.user_id WHERE u.id = :user_id";
    $accessQuery = "SELECT ra.id, a.name FROM role_access ra "
        ."JOIN accessdata a ON ra.access_id = a.id "
        ."JOIN user_role ur ON ur.role_id = ra.role_id "
        ."JOIN userdata u ON u.id = ur.user_id WHERE u.id = :user_id";

    $this->tabs = array(
      'roles' => 
        array (
            'name' => 'Roles',
            'tableName' => 'user_role',
            'query' => $rolesQuery,
            'columns' => array('id', 'name'),
            'relKey' => 'user_id',
            'formColumns' => array('id', 'user_id', 'role_id'),
          ),
      'access' =>
        array (
            'name' => 'Access',
            'tableName' => 'role_access',
            'query' => $accessQuery,
            'columns' => array('id', 'name'),
            'relKey' => 'user_id',
          ),
      );
  }

  public function configureForm()
  {
    parent::configureForm();
    $this->form->setFieldOptions(array(
      'first_login' => array('1' => 'Yes', '0' => 'No'),
      'is_admin' => array('1' => 'Yes', '0' => 'No'),
      ));
  }

}
