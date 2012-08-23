<?php

namespace PetakUmpet;

/* UNFINISHED idea is for authentication backend 
 * so authentication can work using multiple method not just through database
 */

class Auth {

  private $backend;
  
  public function __construct($backend='\\PetakUmpet\\User')
  {
    $this->backend = new $backend;

  }

}