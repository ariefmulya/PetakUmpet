<?php
namespace PetakUmpet;

class Template {

  private $baseViewDir;
	private $layout;

  private $request;
  private $session;
  private $config;

  private $UI;
  private $menu;

  private $blocks;

  private $css;
  private $js;
	
	public function __construct(Request $request, Session $session, Config $config)
	{
		$this->request = $request;
		$this->session = $session;
    $this->config  = $config;

    $this->baseViewDir = PU_DIR . DS . 'app' . DS . $this->request->getApplication() . DS . 'View' . DS ;

    $this->css = array();
    $this->js = array();
    $this->blocks = array();

    $this->UI = new UI($this->request, $this->session, $this->config);
	}

	public function render($view, $variables=array(), $renderLayout=null)
	{
    // set a layout, but allow views to change it later
    $this->setLayout($renderLayout);

		/* XXX at this point existing member vars of 
		this class will be available to template XXX */	
		extract(get_object_vars($this));
		extract($variables);
		$T = $this;

		// response contents
		$response = new Response;
		$response->render($view, $variables, $this);

		// no layout on Ajax Call or when it set to false
		if ($this->request->isSecureAjax() || $renderLayout===false) {
      foreach ($this->blocks as $content) {
        echo $content;
      }
			exit();
		}

		// setting layout
		Logger::log('Layout: rendering  using ' . $this->layout, Logger::DEBUG);

		// render them
		require($this->layout);
	}

	public function setLayout($layout=null)
	{
		$this->layout =  $this->baseViewDir . ($layout === null ? 'layout' : $layout) . '.php' ;
	}

  public function url($page)
  {
    return $this->request->getAppUrl($page);
  }

  public function getResourceUrl($value)
  {

    return $this->request->getResourceBaseUrl() . $value;
  }

  public function includeFile($name, $variables = array())
  {
  	$file = $this->baseViewDir. str_replace('/', DS, $name) . '.php';

  	if (is_file($file)) {
      $T = $this;
      extract($variables);
    	extract(get_object_vars($this));
  		include_once $file;
  	}
  }

  public function snippet($name, $variables = array())
  {
    return $this->includeFile('Snippet/' . $name, $variables);
  }

  public function block($v)
  {
    echo $this->blocks[$v];
  }

  public function blockStart($block)
  {
    $this->blocks[$block] = $block;
    ob_start();
  }

  public function blockEnd($block)
  {
    $this->blocks[$block] = ob_get_clean();
  }

  public function addCss($vals)
  {
    if (!is_array($vals)) {
      $this->css[] = $vals;
    } else {
      $this->css = array_merge($this->css, $vals);
    }
  }

  public function addJs($vals)
  {
    if (!is_array($vals)) {
      $this->js[] = $vals;
    } else {
      $this->js = array_merge($this->js, $vals);
    }
  }

  public function getCss()
  {
    foreach ($this->css as $c) {
      echo '<link href="' . $this->getResourceUrl('css/' . $c . '.css') . '" rel="stylesheet">';
    }
  }

  public function getJs()
  {
    foreach ($this->js as $j) {
      echo '<script src="' . $this->getResourceUrl('js/' . $j . '.js') . '"></script>';
    }
  }

}