<?php

namespace AppName;

use PetakUmpet\Application;
use PetakUmpet\Response;
use PetakUmpet\Singleton;
use PetakUmpet\Database;
use PetakUmpet\Database\Builder;
use PetakUmpet\Database\Accessor;
use PetakUmpet\User;

class LoginApplication extends Application {

  public function indexAction() 
  {
    $form = new Form\LoginForm;

    if ($this->request->isPost()) {

      if ($form->bindValidate($this->request)) {

        $user = new User($form->getName(), $form->getPassword());

        if ($user->validate()) {
          $this->session->setUser($user);
          $this->session->setUsername($user->getName());
          $this->session->setUserid($user->getId());
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
