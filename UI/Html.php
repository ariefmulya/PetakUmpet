<?php

namespace PetakUmpet\UI;

class Html extends UI {

  public function render()
  {
    foreach ($this->blocks as $b) {
      $b->render();
    }
    
  }
  
}
