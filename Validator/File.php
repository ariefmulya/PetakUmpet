<?php

namespace PetakUmpet\Validator;

use PetakUmpet\Singleton;

class File extends Base {

  public function __construct()
  {
    $this->errorText = 'Please input the filename';
  }
  
  public function check($value=null, $field=null)
  {
    $config = Singleton::acquire('\\PetakUmpet\\Config');

    $uploadedFilename = $config->getUploadFolder() . $_FILES[$this->name]['name'];
    if (move_uploaded_file($_FILES[$this->name]['tmp_name'], $uploadedFilename)) {
      return true;
    }
  
    return false;
  }
  
}
