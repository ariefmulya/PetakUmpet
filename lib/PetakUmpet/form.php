<?php


function form_create($name='Form', $fields = array())
{
  $form = array();
  $form['name'] = $name;

  if (count($fields) > 0) {
    $form['fields'] = $fields;
  }

  return $form;
}

function form_field_input(&$form, $name='input_text', $values='', $extra='')
{
  $form['fields'][$name] = array(
    'values' => $values,
    'extra' => $extra,
    'type'  => 'input',
  );
}

function form_field_password(&$form, $name='password', $doublecheck=true, $extra='')
{
  $form['fields'][$name] = array(
    'doublecheck' => $doublecheck,
    'extra' => $extra,
    'type'  => 'password',
  );
}

function form_field_hidden(&$form, $name='hidden_field', $values='', $extra='')
{
  $form['fields'][$name] = array(
    'values' => $values,
    'extra' => $extra,
    'type'  => 'hidden',
  );
}

function form_field_textarea(&$form, $name='input_textarea', $values='', $extra='', $advance=true)
{
  $form['fields'][$name] = array(
    'values' => $values,
    'extra' => $extra,
    'type'  => 'textarea',
    'advance' => $advance
  );
}

function form_field_select(&$form, $name='input_select', $options = array(), $values=array(), $multiple=false, $extra='')
{
  $form['fields'][$name] = array(
    'values' => $values,
    'extra' => $extra,
    'options' => $options,
    'multiple' => $multiple,
    'extra' => $extra,
    'type' => 'select',
  );
}

function form_echo_label($label)
{
  echo '<label for="'.$label.'">'.ucwords(str_replace("_", " ", $label)).'</label> ';
}

function form_draw_field_input($name, $field)  
{
  $values = (isset($field['values']) ? $field['values'] : '');
  $extra = (isset($field['extra']) ? $field['extra'] : '');

  $tag =  '<input type="text" name="frm['.$name.']" id="'.$name.'" value="__DEFAULT_FIELD_VALUE__" ' . $extra . ' >';

  form_echo_label($name);
  echo str_replace("__DEFAULT_FIELD_VALUE__", htmlentities($values, ENT_QUOTES), $tag) ;
}

function form_draw_field_password($name, $field)  
{
  $doublecheck = (isset($field['doublecheck']) ? $field['doublecheck'] : '');
  $extra = (isset($field['extra']) ? $field['extra'] : '');

  $tag =  '<input type="password" name="frm['.$name.']" id="'.$name.'" value="" ' . $extra . ' >';

  form_echo_label($name);
  echo $tag; 

  if ($doublecheck) {
    form_echo_label('Re-enter password');
    echo '<input type="password" name="frm[reenter_password]" id="reenter_password" value="" ' . $extra . ' >';
  }
}

function form_draw_field_hidden($name, $field)  
{
  $values = (isset($field['values']) ? $field['values'] : '');
  $extra = (isset($field['extra']) ? $field['extra'] : '');

  $tag =  '<input type="hidden" name="frm['.$name.']" id="'.$name.'" value="__DEFAULT_FIELD_VALUE__" ' . $extra . ' >';
  echo str_replace("__DEFAULT_FIELD_VALUE__", htmlentities($values, ENT_QUOTES), $tag) ;
}

function form_draw_field_textarea($name, $field)  
{
  $values = (isset($field['values']) ? $field['values'] : '');
  $extra = (isset($field['extra']) ? $field['extra'] : '');

  $tag =  '<textarea name="frm['.$name.']" id="'.$name.'" '.$extra.' >__DEFAULT_FIELD_VALUE__</textarea>';

  form_echo_label($name);
  echo str_replace("__DEFAULT_FIELD_VALUE__", htmlentities($values, ENT_QUOTES), $tag) ;
  if ($field['advance'] === true) {
    echo '<script type="text/javascript">CKEDITOR.replace(\'' . $name . '\');</script>';
  }
}

function form_draw_field_select($name, $field)
{
  $values = (isset($field['values']) ? $field['values'] : '');
  $extra = (isset($field['extra']) ? $field['extra'] : '');
  $multiple = (isset($field['multiple']) ? $field['multiple'] : '');
  $options = (isset($field['options']) ? $field['options'] : '');

  $start = '<select name="frm['.$name.'][]" id="'.$name.'" '.($multiple ? 'multiple="multiple"' : '').' >';

  $opt = $multiple ? '' : '<option value="NULL">---</option>';

  foreach ($options as $o => $v) {
    if ((is_array($values) && in_array($o, $values)) || $o == $values) {
      $opt  .= '<option value="'.$o.'" selected>'.$v.'</option>';
    } else {
      $opt  .= '<option value="'.$o.'">'.$v.'</option>';
    }
  }
  $end = '</select>';

  form_echo_label($name);
  echo $start . $opt . $end;
}

function form_html_decode(&$frmdata)
{
  foreach ($frmdata as &$f) {
    if (is_array($f)) {
      array_map('util_entity_decode', $f);
    } else {
      $f =  util_entity_decode($f);
    }
  }
  return $frmdata;
}

function form_get_values($request)
{
  return form_html_decode(request_getvar_or_404($request, 'frm'));
}

function form_bind_values(&$form, $values=array())
{
  foreach ($form['fields'] as $f => &$a) {
    if (in_array($f, array_keys($values))) {
      $a['values'] = $values[$f];
    }
  }
}

function form_get_values_bind(&$form, $request)
{
  $a = form_get_values($request);
  form_bind_values($form, $a);
  return $a;
}

function form_display(&$form)
{
  echo '<form class="puForm" id="'.$form['name'].'" action="" method="post">' ."\n";
  echo '<fieldset>' . "\n"; 
  echo '<legend>' . $form['name'] . '</legend>' . "\n";

  foreach ($form['fields'] as $f => $a) {
    echo '<p>' ;
    call_user_func('form_draw_field_' . $a['type'], $f, $a);
    echo '</p>';
  }

  echo '</fieldset>' . "\n";
  echo '<p class="submit"><button type="submit">Submit</button></p>' . "\n";
  echo '</form>' . "\n";
}

