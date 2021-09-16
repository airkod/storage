<?php

namespace App\Controller;

use Light\Controller;
use Light\Front;

/**
 * Class Api
 * @package App\Controller
 */
class Api extends Controller
{
  /**
   * @return array
   */
  public function uploadFileApi(): array
  {
    $files = $this->mapFiles($_FILES);

    $errors = [];

    $config = Front::getInstance()->getConfig();

    $copiedFiles = [];

    foreach ($files as $file) {

      try {

        $folder = realpath($config['fs']['path']) . $this->getRequest()->getPost('path') . '/' . $this->getRequest()->getPost('folder');

        if (!file_exists($folder)) {
          mkdir($folder);
        }

        $fileNameParts = explode('.', $file['name']);
        $fileName = md5(microtime()) . '.' . array_pop($fileNameParts);

        $copiedFiles[] = $config['fs']['url'] . $this->getRequest()->getPost('path') . '/' . $this->getRequest()->getPost('folder') . '/' . $fileName;
        copy($file['tmpName'], $folder . '/' . $fileName);
      } catch (\Exception $e) {
        $errors[] = 'File "' . $file['name'] . '" was not uploaded. Reason: ' . $e->getMessage();
      }
    }

    return [
      'success' => count($errors) == 0,
      'files' => $copiedFiles,
      'errors' => $errors
    ];
  }

  /**
   * @return array
   */
  public function uploadFileBase64(): array
  {
    $config = Front::getInstance()->getConfig();

    $copiedFiles = [];

    foreach ($this->getParam('files') as $file) {

      $folder = realpath($config['fs']['path']) . '/' . $this->getRequest()->getPost('path'); ;
      if (!file_exists($folder)) {
        mkdir($folder);
      }

      $folder = $folder  . '/' . $this->getRequest()->getPost('folder');
      if (!file_exists($folder)) {
        mkdir($folder);
      }

      $fileNameParts = explode('/', $file['mime']);
      $fileName = md5(microtime()) . '.' . array_pop($fileNameParts);

      $content = base64_decode($file['content']);
      file_put_contents($folder . '/' . $fileName, $content);

      $copiedFiles[] = $config['fs']['url'] . '/' . $this->getRequest()->getPost('path') . '/' . $this->getRequest()->getPost('folder') . '/' . $fileName;
    }
    return $copiedFiles;
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