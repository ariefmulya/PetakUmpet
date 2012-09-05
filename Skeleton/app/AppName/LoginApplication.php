<?php

namespace AppName;

use PetakUmpet\Application;
use PetakUmpet\Response;
use AppName\AppUser

class LoginApplication extends Application {

  public function indexAction() 
  {
    $form = new Form\LoginForm;

    if ($this->request->isPost()) {

      if ($form->bindValidate($this->request)) {

        $user = new AppUser($form->getFieldValue('name'), $form->getFieldValue('password'));

        if ($user->validate()) {
          $this->session->setUser($user);
          $this->session->set('username', $user->getName());
          $this->session->set('userid', $user->getUserid());
          $this->session->setAuthenticated(true);
          // authenticated, go to index
          $this->redirectToStartPage();
        }
        // failed login
        $this->session->setFlash('Login failed, please check username or password.');
      }
    }

    return $this->render(array('form' => $form));
  }
}
