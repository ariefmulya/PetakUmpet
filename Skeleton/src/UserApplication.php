<?php

use PetakUmpet\Application;

use PetakUmpet\Form\DBConnector;

class UserApplication extends Application {

  public function profileAction()
  {
    $dbf = new DBConnector('userdata');
    $dbf->importById($this->session->getUserid());

    if ($this->request->isPost()) {
      if (!$dbf->bindValidateSave($this->request)) {    
      }
    }

    return $this->render('User/profile', array('form' => $dbf));
  }

}