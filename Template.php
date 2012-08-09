<?php
namespace PetakUmpet;

class Template {

  private $baseViewDir;
	private $layout;

  private $request;
  private $session;
  private $config;
	
	function __construct(Request $request, Session $session, Config $config)
	{
		$this->request = $request;
		$this->session = $session;
    $this->config  = $config;

    $this->baseViewDir = PU_DIR . DS . 'app' . DS . $this->request->getApplication() . DS . 'View' . DS ;
	}

	function render($view, $variables=array(), $layout=null)
	{
    $app = $this->request->getApplication();

		/* XXX at this point existing member vars of 
		this class will be available to template XXX */	
		extract(get_object_vars($this));
		extract($variables);
		$T = $this;

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
		$this->layout =  $this->baseViewDir . ($layout === null ? 'layout' : $layout) . '.php' ;
	}

  public function link($name, $page, $class="", $icon=null)
  {
    $page = str_replace('/', '&a=', $page);

    $href = $this->request->getAppUrl($page);

    return '<a class="'.$class.'" href="'.$href.'">' . 
           ($icon === null ? '' : '<i class="'.$icon.'"></i>&nbsp;') . $name . '</a>';
  }

  public function dropdown($name, $li, $class="", $icon=null)
  {
    $r  = '<li class="dropdown">';
    $r .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#">'.$name.' <b class="caret"></b></a>';
    $r .= '<ul class="dropdown-menu">';

    foreach($li as $k=>$v) {
      $r .= ($v == '#')? '<li class="nav-header">'.$k.'</li>':'<li>'.$this->link($k, $v, '#navs').'<li>';
    }

    $r .= '</ul></li>';
    
    return $r;
  }

  public function url($page)
  {
    return $this->request->getAppUrl($page);
  }

  public function getResourceUrl($value)
  {

    return $this->request->getResourceBaseUrl() . $value;
  }

  public function navMenu($menu, $defaultPage='Home/index')
  {
    $page = $this->request->getPage();

    if (in_array($page, $menu)) {
      $this->session->set('lastActivePage', $page); 
    } else {
      $page = $this->session->get('lastActivePage');
      if ($page === null) $page = $defaultPage;
    }    
    $s = '<ul class="nav">';
    foreach ($menu as $k=>$v) {
      $li_class = '';
      
      if ($page == $v) $li_class=' class="active" ';      
      $s .= '  <li' . $li_class . '>' . $this->link($k, $v) . '</li>';      
    }
    $s .= '</ul>';
    return $s;
  }

  public function subNavMenu()
  {
    $subNavMenu = $this->request->getSubNavMenu(); 
    if ($subNavMenu === false) return '';
    
    if (!is_array($subNavMenu) || count($subNavMenu) <= 0) {
      $subNavMenu = $this->session->getSubNavMenu();
    } else {
      $this->session->setSubNavMenu($subNavMenu);
    }

    $s = '';
    if (is_array($subNavMenu) && count($subNavMenu) > 0) {
      $s = '<div class="subnav subnav-fixed">'
          . '<ul class="nav nav-pills">'
          ;
      foreach ($subNavMenu as $name => $action) {
        if (is_array($action)) {
          $s .= $this->dropdown($name, $action);
        } else {
          $s .= '<li>' . $this->link($name, $action) . '</li>';
        }
      }
      $s .= '</ul></div>';
    }
    return $s;
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

}