<?php

namespace AppName\Form;

use PetakUmpet\Form;
use PetakUmpet\Validator;
use PetakUmpet\Validator\Required;

class LoginForm extends Form {

  public function __construct($name='Login')
  {
    parent::__construct($name);

    $this->add('text', 'name', array('required' => 'required', 'focus' => 'focus'));
    $this->add('password', 'password', array('required' => true));

    $this->addAction(new Form\Field\Submit('Login'));

    $vld = new Validator;
    $vld->set('name', new Required);
    $vld->set('password', new Required);

    $this->setValidator($vld);

  } 

}
