<?php

namespace App\Controller;

use Light\Controller;
use Light\Cookie;
use Light\Front;

/**
 * Class Base
 * @package App\Controller
 */
abstract class Base extends Controller
{
  /**
   * @throws \Exception
   */
  public function init()
  {
    parent::init();

    if (Front::getInstance()->getConfig()['key'] == $this->getParam('key')) {
      Cookie::set('authorized', $this->getParam('key'));
    }

    if (!Cookie::get('authorized')) {
      throw new \Exception('not authorized');
    }
  }
}