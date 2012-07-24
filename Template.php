<?php
namespace PetakUmpet;

class Template {

	private $layout;
	
	function __construct()
	{
		$this->request = Singleton::acquire('\\PetakUmpet\\Request'); 
		$this->session = Singleton::acquire('\\PetakUmpet\\Session'); 
	}

	function render($view, $variables=null, $layout=null)
	{
		/* XXX at this point existing member vars of 
		this class will be available to template XXX */	
		extract(get_object_vars($this));
		extract($variables);
		$T = $this;

		// configuration variables
		$ProjectTitle = Configuration::ProjectTitle;

		// response contents
		$response = new Response;
		$__mainContents = $response->render($view, $variables, $this);

		// no layout on Ajax Call
		if ($this->request->isSecureAjax()) {
			echo $__mainContents;
			exit();
		}

		// setting layout
		$this->setLayout($layout);
		Logger::log('Layout: rendering  using ' . $this->layout);

		// render them
		include_once($this->layout);
	}

	function setLayout($layout=null)
	{
		$this->layout = PU_DIR . DS . 'res' . DS . 'View' . DS . ($layout === null ? 'layout' : $layout) . '.php' ;
	}

  public function link($name, $page, $class="")
  {
    $page = str_replace('/', '&a=', $page);
    return '<a class="'.$class.'" href="index.php?m='.$page.'">' . $name . '</a>';
  }

  public function navMenu($menu, $selected)
  {
    $s = '';

    foreach ($menu as $k=>$v) {
      $li_class = '';
      
      if ($selected == $v) $li_class=' class="active" ';

      $s .= '<ul class="nav">';
      $s .= '  <li' . $li_class . '>' . $this->link($k, $v) . '</li>';
      $s .= '</ul>';
    }
    return $s;
  }

  public function snippet($name)
  {
  	$snippet_file = PU_DIR . DS . 'res' . DS . 'View' . DS . 'Snippet' . DS . $name . '.php';
  	if (is_file($snippet_file)) {
      $T = $this;
    	extract(get_object_vars($this));
  		include_once $snippet_file;
  	}
  }

}