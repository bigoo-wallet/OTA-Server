<?php
/**
 * 文件操作
 * @author tytymnty@gmail.com
 * @since 2016-07-07 16:15:45
 */

namespace Growler\Libs;
use Intervention\Image\ImageManagerStatic as Image;

class File
{
  
  /**
   * OTA文件上传
   * @param string $name 文件域名称
   * @param string $path 新的文件的子路径
   * @return array|bool 
   */
  public static function otaFileUpload($name, $path)
  {
    try {
      $arr = preg_split('/\./', $_FILES[$name]['name']);

      $pathInfo = preg_split('/\//', $path);
      $filename = array_pop($pathInfo);
      $folder = implode('/', $pathInfo);

      $subPath = "files/ota/{$folder}";
      $fullPath = PROJECT_ROOT . "/{$subPath}";
      
      if (!file_exists($fullPath)) {
        mkdir($fullPath, 0777, true);
      }

      $storage = new \Upload\Storage\FileSystem($fullPath);
      $file = new \Upload\File($name, $storage);

      $file->setName($filename);

      $file->addValidations(array(
          new \Upload\Validation\Size('100M')
      ));

      $size = $file->getSize();
      $md5 = $file->getMd5();
      $ext = $file->getExtension();


      $oFilePath = "{$fullPath}/{$filename}.{$ext}";
      if (file_exists($oFilePath)) {
        unlink($oFilePath);
      }
      
      $file->upload();

      $result = [
        'size'    => $size,
        'md5'     => $md5,
        'subPath' => "{$folder}/{$filename}.{$ext}"
      ];

      return $result;

    } catch (\Exception $e) {
        return false;
    }
  }

  /**
   * 图片文件上传
   * @param string $name 文件名称
   * @param string $newFilename 目标名称（hash前）
   * @return string|bool 
   */
  public static function imgUpload($name, $newFilename = null)
  {
    try {

      if (empty($newFilename)) {
        $newFilename = uniqid();
      } 
      $newFilename = substr(md5($newFilename), 8, 16);
      $d1 = substr($newFilename, 0, 2);
      $d2 = substr($newFilename, 2, 2);

      $hashPath = "{$d1}/{$d2}";
      $subPath = "files/img/{$hashPath}";
      $fullPath = PROJECT_ROOT . "/{$subPath}";

      if (!file_exists($fullPath)) {
        mkdir($fullPath, 0777, true);
      }

      $storage = new \Upload\Storage\FileSystem($fullPath);
      $file = new \Upload\File($name, $storage);

      $file->setName($newFilename);

      $file->addValidations(array(
          new \Upload\Validation\Mimetype(['image/png', 'image/jpeg', 'image/jpg', 'image/gif']),
          new \Upload\Validation\Size('5M')
      ));

      $file->upload();

      return "{$hashPath}/$newFilename." . $file->getExtension();
    } catch (\Exception $e) {
        return false;
    }
  }

  /**
   * 图片缩放
   * @param string $path
   * @param int $width 要缩放的宽度
   */
  public static function imageResize($path, $width)
  {
    $rootPath = PROJECT_ROOT . '/files/img/';
    $imgPath = "{$rootPath}/{$path}";

    $img = Image::make($imgPath);

    $ow = $img->getWidth();
    $oh = $img->getHeight();

    if ($ow > $width) {

      // 原图大于缩放宽度，缩放
      $dw = $width;
      $dh = round($width / $ow * $oh + 0.5);

      $img->resize($dw, $dh);
    } else {
      $dw = $ow;
      $dh = $oh;
    }

    $newPath = "{$img->dirname}/{$img->filename}_{$width}.{$img->extension}";
    $img->save($newPath);
    return true;
  }
}