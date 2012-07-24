<?php

namespace PetakUmpet;

abstract class Application {

  protected $router;
  protected $request;
  protected $session;

  public function __construct(Process $process, Request $request, Session $session)
  {
    $this->process = $process;
    $this->request = $request;
    $this->session = $session;
  }

  public function render($view=null, $variables=array(), $layout=null)
  {
    $T = new Template;
    return $T->render($view, $variables, $layout);
  }

  public function redirect($page)
  {
    return $this->process->redirect($page);
  }
  

}