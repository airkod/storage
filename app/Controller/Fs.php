<?php

namespace App\Controller;

use Light\Front;

/**
 * Class Fs
 * @package App\Controller
 */
class Fs extends Base
{
  /**
   * @return array
   */
  public function createFolder(): array
  {
    $config = Front::getInstance()->getConfig();
    mkdir(realpath($config['fs']['path']) . $this->getRequest()->getPost('path') . '/' . $this->getRequest()->getPost('name'));

    return [];
  }

  /**
   * @return array
   */
  public function deleteFolder(): array
  {
    $config = Front::getInstance()->getConfig();
    $dirPath = realpath($config['fs']['path']) . $this->getRequest()->getGet('path');

    self::deleteDir($dirPath);

    return [];
  }

  /**
   * @return array[]
   */
  public function uploadFile(): array
  {
    $files = $this->mapFiles($_FILES);

    $errors = [];

    $config = Front::getInstance()->getConfig();

    foreach ($files as $file) {

      try {
        copy($file['tmpName'], realpath($config['fs']['path']) . $this->getRequest()->getPost('path') . '/' . $file['name']);
      } catch (\Exception $e) {
        $errors[] = 'File "' . $file['name'] . '" was not uploaded.';
      }
    }

    return ['errors' => $errors];
  }

  /**
   * @return array
   */
  public function deleteFile(): array
  {
    $config = Front::getInstance()->getConfig();
    $dirPath = realpath($config['fs']['path']) . $this->getRequest()->getGet('path');

    unlink($dirPath);

    return [];
  }

  /**
   * @return array
   */
  public function uploadByUrl(): array
  {
    $name = md5(microtime());

    $file = file_get_contents($this->getRequest()->getPost('url'));

    $config = Front::getInstance()->getConfig();

    $filePath = realpath($config['fs']['path']) . $this->getRequest()->getPost('path') . '/';

    file_put_contents($filePath . $name, $file);

    $mimes = new \Mimey\MimeTypes();
    $extension = $mimes->getExtension(mime_content_type($filePath . $name));

    rename($filePath . $name, $filePath . $name . '.' . $extension);

    return [];
  }

  /**
   * @param string $dirPath
   */
  public static function deleteDir($dirPath)
  {
    if (!is_dir($dirPath)) {
      throw new \InvalidArgumentException("$dirPath must be a directory");
    }

    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
      $dirPath .= '/';
    }

    $files = glob($dirPath . '*', GLOB_MARK);

    foreach ($files as $file) {

      if (is_dir($file)) {
        self::deleteDir($file);
      } else {
        unlink($file);
      }
    }

    rmdir($dirPath);
  }

  /**
   * @param array $files
   * @return array
   */
  public function mapFiles(array $files = []): array
  {
    return array_map(function ($name, $type, $tmpName, $error, $size) {
      return [
        'name' => $name,
        'type' => $type,
        'tmpName' => $tmpName,
        'error' => $error,
        'size' => $size
      ];

    }, $_FILES['files']['name'],
      $_FILES['files']['type'],
      $_FILES['files']['tmp_name'],
      $_FILES['files']['error'],
      $_FILES['files']['size']
    );
  }
}
