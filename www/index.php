<?php

require_once __DIR__ . '/../vendor/autoload.php';

echo \Light\Front::getInstance(require_once '../config.php')
    ->bootstrap()
    ->run();