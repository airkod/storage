<?php

namespace App\Controller;

use Exception;
use Light\Front;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class Index
 * @package App\Controller
 */
class Index extends Base
{
  /**
   * @throws Exception
   */
  public function init()
  {
    parent::init();

    $this->getView()->assign('path',
      $this->getRequest()->getGet('path',
        $this->getRequest()->getPost('path', '/')));

    if ($this->getRequest()->getGet('ajax') == 'true' || $this->getRequest()->isAjax()) {
      $this->getView()->setLayoutEnabled(false);
    }
  }

  public function search()
  {
    try {
      $config = Front::getInstance()->getConfig();

      $path = $config['fs']['path'];

      if ($this->getRequest()->getGet('path')) {
        $path = $path . '/' . $this->getRequest()->getGet('path');
      }

      $it = new RecursiveDirectoryIterator(realpath($path));

      $files = [];

      foreach (new RecursiveIteratorIterator($it) as $file) {

        $fileName = substr(realpath((string)$file), strlen(realpath($path)));

        if (empty($fileName)) {
          continue;
        }

        if (strpos(basename($fileName), $this->getRequest()->getGet('query')) !== false) {

          $files[] = [
            'name' => basename($fileName),
            'path' => $fileName,
            'time' => filemtime(realpath($file)),
            'mime' => mime_content_type(realpath($file)),
            'url' => $config['fs']['url'] . $fileName
          ];
        }
      }
    } catch (Exception $e) {
      $files = [];
    }

    $this->getView()->setVars([
      'files' => $files,
      'query' => $this->getRequest()->getGet('query')
    ]);
  }

  public function index()
  {
  }

  public function update()
  {
  }

  public function tree()
  {
  }
}