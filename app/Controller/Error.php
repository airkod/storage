<?php

namespace App\Controller;

use Gumlet\ImageResize;
use Light\ErrorController;
use Light\Exception\ControllerClassWasNotFound;
use Light\Front;

/**
 * Class Error
 * @package App\Controller
 */
class Error extends ErrorController
{
  /**
   * @return array
   */
  public function index()
  {
    $exception = $this->getException();

    if ($exception instanceof ControllerClassWasNotFound) {
      $this->propagateImage();
    }

    $this->getResponse()->setStatusCode(200);

    return [
      'error' => true,
      'message' => $this->getException()->getMessage()
    ];
  }

  private function propagateImage()
  {
    try {
      $dest = str_replace('/storage', Front::getInstance()->getConfig()['fs']['path'], $_SERVER['REQUEST_URI']);
      $realFileName = explode('/', $dest);
      $realFileName = $realFileName[count($realFileName) - 1];
      $realFileName = explode('_r_', $realFileName);
      $ext = explode('.', $realFileName[1]);

      if ($ext[1] !== 'png' && $ext[1] !== 'jpg' && $ext[1] !== 'jpeg') {
        return;
      }

      $sourceFolder = explode('/', $dest);
      unset($sourceFolder[count($sourceFolder) - 1]);
      $source = implode('/', $sourceFolder) . '/' . $realFileName[0] . '.' . $ext[1];

      if (!file_exists($source)) {
        return;
      }

      $width = null;
      $height = null;

      try {
        $width = (int)explode('x', $ext[0])[0];
      } catch (\Exception $e) {
      }

      try {
        $height = (int)explode('x', $ext[0])[1];
      } catch (\Exception $e) {
      }

      $image = new ImageResize($source);

      if ($width && $height) {
        $image->crop($width, $height, true);
      }
      else {
        $image->resizeToLongSide($width, false);
      }
      $image->save($dest);

      $this->redirect($_SERVER['REQUEST_URI']);

    } catch (\Exception $e) {
    }
  }
}