<?php

use PetakUmpet\Application;
use Config\Config as Config;

class LogoutApplication extends Application {

  public function indexAction()
  {
    $this->session->destroy();
    $this->redirect(Config::LoginPage);
  }
}