<?php

namespace PetakUmpet;

/* How does our UI works?

Application->render
           ->setTemplate
           ->Template->render
             Template->setUI 
                     ->setLayout
                     ->render
                       Response->render


*/
                       
class UI {

  private $request;
  private $session;
  private $config;

  public function __construct(Request $request, Session $session, Config $config)
  {
    $this->request = $request;
    $this->session = $session;
    $this->config  = $config;
  }

  public function setMenu($menu)
  {     
    $this->menu = new Menu($menu);
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
    $s = '<ul class="nav navbar-nav">';
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
    return '';
    $subNavMenu = $this->request->getSubNavMenu(); 
    if ($subNavMenu === false) return '';
    
    if (!is_array($subNavMenu) || count($subNavMenu) <= 0) {
      $subNavMenu = $this->session->getSubNavMenu();
    } else {
      $this->session->setSubNavMenu($subNavMenu);
    }

    $s = '';
    if (is_array($subnavmenu) && count($subnavmenu) > 0) {
      $s = '<div class="navbar navbar-default"><div class="container">'
          . '<ul class="nav navbar-nav">'
          ;
      foreach ($subnavmenu as $name => $action) {
        if (is_array($action)) {
          $s .= $this->dropdown($name, $action);
        } else {
          $s .= '<li>' . $this->link($name, $action) . '</li>';
        }
      }
      $s .= '</ul></div></div>';
    }
    return $s;
  }
  
  public function buttonlink($name, $page, $class="", $icon=null)
  {
    $page = str_replace('/', '&a=', $page);

    $href = $this->request->getAppUrl($page);

    return '<a href="'.$href.'" class="btn btn-default '.$class.'">'.$name.'</a>';
  }

  public function link($name, $page, $class="", $icon=null)
  {
    $href = $this->config->getRoutingLinkFromPage($page);

    return '<a class="'.$class.'" href="'.$href.'">' . 
           ($icon === null ? '' : '<i class="'.$icon.'"></i>&nbsp;') . $name . '</a>';
  }

  public function appLink($app, $name, $page, $class="", $icon=null)
  {
    $page = str_replace('/', '&a=', $page);

    $href = $this->request->getBaseUrl() . '/' . $app . '?' . $page;

    return '<a class="'.$class.'" href="'.$href.'">' . 
           ($icon === null ? '' : '<i class="'.$icon.'"></i>&nbsp;') . $name . '</a>';
  }

  public function dropdown($name, $li, $class="", $icon=null)
  {
    $r  = '<li class="dropdown">';
    $r .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#">'.$name.' <b class="caret"></b></a>';
    $r .= '<ul class="dropdown-menu">';

    foreach($li as $k=>$v) {
      $r .= ($v == '#')? '<li>'.$k.'</li>':($v == '--')? '<li class="divider"></li>':'<li>'.$this->link($k, $v, '#navs').'<li>';
    }

    $r .= '</ul></li>';
    
    return $r;
  }
  
}
