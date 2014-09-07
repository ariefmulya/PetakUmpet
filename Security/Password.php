<?php 

namespace PetakUmpet\Security;

use PetakUmpet\External\PasswordHash;

abstract class Password {

  public function create($password) 
  {
    $hf = new PasswordHash(8, false);
    $hpass = $hf->HashPassword($password); 

    return $hpass;
  }

  public function check($password, $hash) 
  {
    $hf = new PasswordHash(8, false);
    return $hf->CheckPassword($password, $hash);
  }

}