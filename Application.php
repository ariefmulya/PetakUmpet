<?php

namespace PetakUmpet;

abstract class Application {

  protected $router;
  protected $request;
  protected $session;
  protected $config;

  public function __construct(Process $process, Request $request, Session $session, Config $config)
  {
    $this->process = $process;
    $this->request = $request;
    $this->session = $session;
    $this->config  = $config;
  }

  protected function renderView($view, $variables=array(), $layout=null)
  {
    $T = new Template($this->request, $this->session, $this->config);

    return $T->render($view, $variables, $layout);
  }

  protected function render($variables=array(), $layout=null)
  {
    $view = $this->request->getPage();

    if ($view == '/') $view = $this->config->getStartPage();
    
    return $this->renderView($view, $variables, $layout);
  }

  public function redirect($page)
  {
    return $this->process->redirect($page);
  }

  public function redirectToStartPage()
  {
    return $this->process->redirectToStartPage();
  }

  public function redirectToLoginPage($extra='')
  {
    return $this->process->redirectToLoginPage($extra);
  }

}