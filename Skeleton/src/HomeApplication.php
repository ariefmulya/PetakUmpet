<?php

use PetakUmpet\Application;

class HomeApplication extends Application {

  public function indexAction()
  {
    return $this->render('index');
  }

  public function aboutAction()
  {
    return $this->render('about');
  }
}