<?php

namespace PetakUmpet;

abstract class Application {

  protected $router;
  protected $request;
  protected $session;

  public function __construct(Router $router, Request $request, Session $session)
  {
    $this->router  = $router;
    $this->request = $request;
    $this->session = $session;
  }

  public function render($view=null, $variables=array(), $layout=null)
  {
    $response = new Response();

    return $response->render($view, $variables, $layout);
  }

  public function redirect($page)
  {
    return $this->router->redirect($page);
  }
  

}