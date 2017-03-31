#!/usr/bin/env php
<?php

function is_assoc(array $in) {
  return ! is_numeric(implode('', array_keys($in)));
}

function key_string(array $keys) {
  $head = reset($keys);
  if (sizeof($keys) > 1) {
    return $head . '[' . implode('][', array_slice($keys, 1)) . ']';
  }
  else {
    return $head;
  }
}

function to_ini_format(array $in, array $keys = []) {
  $out = array();

  foreach ($in as $key => $value) {
    $keys[] = $key;

    if (is_array($value)) {
      if (is_assoc($value)) {
        $out = array_merge($out, to_ini_format($value, $keys));
      }
      else {
        foreach ($value as $j) {
          $out[] = key_string($keys) . '[] = ' . $j;
        }
      }
    }
    else {
      $out[] = key_string($keys) . ' = ' . $value;
    }

    array_pop($keys);
  }

  return $out;
}

$files = glob('*.make.yml');
foreach ($files as $file) {
  $parsed = yaml_parse_file($file);
  $out = fopen(basename($file, '.yml'), 'w');

  if (isset($parsed['includes'])) {
    $parsed['includes'] = array_map(function($include) { return basename($include, '.yml'); }, $parsed['includes']);
  }

  foreach (to_ini_format($parsed) as $line) {
    fwrite($out, "$line\n");
  }
  fclose($out);
}
