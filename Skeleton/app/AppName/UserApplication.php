<?php

namespace AppName;

use PetakUmpet\Application;

use PetakUmpet\Form\Component\TableAdapterForm;

class UserApplication extends Application {

  public function profileAction()
  {
    $dbf = new TableAdapterForm('userdata', array('id', 'name', 'password'));

    $dbf->setFormOptions(array('is_admin' => array('1' => 'Ya', '0' => 'Tidak')));
    $dbf->setValuesById($this->session->getUser()->getId());

    if ($this->request->isPost()) {
      if ($dbf->bindValidateSave($this->request)) {    
        $this->session->setFlash('Data is saved.');
      }
    }

    return $this->render(array('form' => $dbf));
  }

  public function noAccessAction()
  {
    $this->render();
  }
}

