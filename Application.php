<?php

namespace PetakUmpet;

abstract class Application {

  protected $router;
  protected $request;
  protected $session;
  protected $config;

  protected $templateObj;
  protected $tplVars;

  public function __construct(Process $process, Request $request, Session $session, Config $config)
  {
    $this->process = $process;
    $this->request = $request;
    $this->session = $session;
    $this->config  = $config;

    $this->tplVars = array();
    $this->templateObj = null;
  }

  protected function setVariable($name, $value)
  {
    $this->tplVars[$name] = $value;
  }

  protected function setTemplate($template)
  {
    $this->templateObj = $template;
  }

  protected function renderView($view, $variables=array(), $layout=null)
  {
    $T = $this->templateObj;
    if ($T === null) {
      $T = new Template($this->request, $this->session, $this->config);
    }

    $vars = array_merge($this->tplVars, $variables);

    return $T->render($view, $vars, $layout);
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