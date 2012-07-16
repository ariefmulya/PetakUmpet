<?php

namespace PetakUmpet\Form;

interface FormField {
  public function getName();
  public function getValue();
  public function setValue();
}