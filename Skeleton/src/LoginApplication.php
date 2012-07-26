<?php

use PetakUmpet\Application;
use PetakUmpet\Response;
use PetakUmpet\Singleton;
use PetakUmpet\Database;
use PetakUmpet\Database\Builder;
use PetakUmpet\Database\Accessor;

class LoginApplication extends Application {

  public function indexAction() 
  {
    $form = new \Form\LoginForm;

    if ($this->request->isPost()) {

      if ($form->bindValidate($this->request)) {

        $dba = new Accessor('userdata');

        $userdata = $dba->findOneBy(array('name' => $form->getName(), 'password' => $form->getPassword()));

        if ($userdata && $userdata['name'] == $form->getName()) {
          $this->session->setUser($userdata['name']);
          $this->session->setUserid($userdata['id']);
          $this->session->setAuthenticated(true);
          // authenticated, go to index
          $this->redirect(\Config::StartPage);

        }
        // failed login
        $this->session->setFlash('Login failed, please check username or password.');
      }
    }

    return $this->render('Login/index', 
      array(
        'form' => $form,
        )
      );
  }

}