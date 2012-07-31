<?php

namespace eYanFar;

use PetakUmpet\Application;

use PetakUmpet\Form\DBConnector;

class UserApplication extends Application {

  public function profileAction()
  {
    $dbf = new DBConnector('userdata');
    $dbf->setType('is_admin', 'checkbox');
    $dbf->setOptions('is_admin', array('1' => ''));
    $dbf->importById($this->session->getUserid());

    if ($this->request->isPost()) {
      if (!$dbf->bindValidateSave($this->request)) {    
      }
    }

    return $this->render(array('form' => $dbf));
  }

  public function noAccessAction()
  {
    $this->render();
  }
}