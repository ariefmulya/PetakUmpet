<?php

namespace eYanFar;

use PetakUmpet\Application;

class HomeApplication extends Application {

  public function indexAction()
  {
    return $this->render();
  }

  public function aboutAction()
  {
    return $this->render();
  }
}