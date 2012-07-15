<?php

// place for utility functions
//

function util_entity_decode($s)
{
  return html_entity_decode($s, ENT_QUOTES);
}

function util_format_link($mod, $act, $title)
{
  echo '<a href="' . request_get_url() . '?mod=' . $mod .(strlen($act) > 0 ? '&act=' . $act  : '') . '">' . $title.'</a>';
}

function util_echo_link($title, $link)
{
  list ($mod, $act) = util_parse_link($link);
  if (!$mod) {
    return false;
  }
  return util_format_link($mod, $act, $title);
}

function util_echo_full_link($title, $link)
{
  echo '<a href="' . $link .'">' . $title . '</a>';
}

function util_parse_link($link)
{
  if (!strstr($link, '/')) {
    return array($link, null);
  }

  $a = explode('/', $link);

  $mod = $a[0];
  $act = (isset($a[1]) ? $a[1] : 'index');

  return array($mod, $act);
}

function util_url_for($link)
{
  list ($mod, $act) = util_parse_link($link);
  return request_get_url() . '?mod=' . $mod . '&act=' . $act;
}

function util_echo_ucwords($s)
{
  echo ucwords(str_replace("_", " ", $s));
}

