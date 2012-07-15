<?php

namespace PetakUmpet;

abstract class Application {

  public function __construct(Request $request, Session $session)
  {
    $this->request = $request;
    $this->session = $session; 
  }

  public function render($view=null, $variables=array())
  {
    $response = new Response($this->request, $this->session);

    return $response->render($view, $variables);
  }
  
}