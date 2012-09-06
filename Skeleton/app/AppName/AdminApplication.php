<?php

namespace AppName;

use PetakUmpet\Application;

class AdminApplication extends Application {

  public function indexAction()
  {
    $this->request->setSubNavMenu(array(
      'User Management' => array(
        'User Data' => 'Userdata/index',
        'Role Data' => 'TableMaster/index&table=roledata',
        'Access Data' => 'TableMaster/index&table=accessdata',
        ),
      ));
    return $this->render();
  }

}
