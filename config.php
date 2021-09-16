<?php

return [
  'light' => [
    'exception' => false,
    'loader' => [
      'path' => realpath(dirname(__FILE__)) . '/app',
      'namespace' => 'App',
    ],
    'asset' => [
      'underscore' => true,
      'prefix' => '/assets'
    ],
  ],
  'fs' => [
    'path' => realpath(dirname(__FILE__)) . '/www/storage',
    'url' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/storage',
    'base' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/'
  ],
  'key' => getenv('STORAGE_KEY')
];