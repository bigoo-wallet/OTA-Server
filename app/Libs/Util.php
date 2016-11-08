<?php

namespace Growler\Libs;

class Util 
{

  /**
   * 生成Token
   */
  public static function token()
  {
    $token = md5(bin2hex(uniqid(rand(), true)));

    $token = '1234';
    return $token;
  }

  /**
    * 验证身份证号
    * @param $vStr
    * @return bool|array
    */
  public static function idNumberCheck($vStr)
  {
    $vCity = array(
      '11', '12', '13', '14', '15', '21', '22',
      '23', '31', '32', '33', '34', '35', '36',
      '37', '41', '42', '43', '44', '45', '46',
      '50', '51', '52', '53', '54', '61', '62',
      '63', '64', '65', '71', '81', '82', '91'
    );

    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr))
      return false;

    if (!in_array(substr($vStr, 0, 2), $vCity))
      return false;

    $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
    $vLength = strlen($vStr);

    if ($vLength == 18) {
        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);

    } else {
        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
    }

    if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday)
      return false;

    if ($vLength == 18) {
        $vSum = 0;

        for ($i = 17 ; $i >= 0 ; $i--) {
          $vSubStr = substr($vStr, 17 - $i, 1);
          $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
        }

        if($vSum % 11 != 1)
          return false;
    }

    # 1男, 2女
    $gender = 2;
    if (intval(substr($vStr, 16, 1)) % 2 == 1) {
      $gender = 1;
    }

    return [$vBirthday, $gender];
  }

  public static function radian($d) {
      return $d * 3.1415926535898 / 180.0;
  }

  /**
   * 计算两个地点之间的距离
   * @param float $lng1
   * @param float $lat1
   * @param float $lng2
   * @param float $lat2
   * @return int 距离 米
   */
  public static function distanceCalculate($lng1, $lat1, $lng2, $lat2) 
  {
    $radLat1 = self::radian( $lat1 );
    $radLat2 = self::radian( $lat2 );
    $a = self::radian( $lat1 ) - self::radian( $lat2 );
    $b = self::radian( $lng1 ) - self::radian( $lng2 );

    $s = 2 * asin ( sqrt (
        pow ( sin ( $a / 2 ), 2 ) + 
        cos ( $radLat1 ) * 
        cos ( $radLat2 ) * 
        pow ( sin ( $b / 2 ), 2 ) 
      ) 
    );
    $s = $s * 6378137; //乘上地球半径，单位为米
    return round($s); //单位为米
  }

  /**
   * 获取附近的地点范围
   * @param float $lng
   * @param float $lat
   * @param int $distance 半径，千米
   * @return array [maxLng, minLng, maxLat, minLat]
   */
  public static function getNearby($lng, $lat, $distance) 
  {
    $radius = 6371.009; // in miles

    // latitude boundaries
    $maxLat = (float) $lat + rad2deg($distance / $radius);
    $minLat = (float) $lat - rad2deg($distance / $radius);

    // longitude boundaries (longitude gets smaller when latitude increases)
    $maxLng = (float) $lng + rad2deg($distance / $radius / cos(deg2rad((float) $lat)));
    $minLng = (float) $lng - rad2deg($distance / $radius / cos(deg2rad((float) $lat)));

    // 查询按距离排序的数据
    // SELECT * FROM table
    //   WHERE 
    //     lat > $minLat AND 
    //     lat < $maxLat AND 
    //     lng > $minLng AND 
    //     lng < $maxLng
    // ORDER BY ABS(lat - $lat) + ABS(lng - $lng) ASC
    // LIMIT 10;

    return [$maxLng, $minLng, $maxLat, $minLat];
  }

  /**
   * 获取当前毫秒
   */
  public static function microtime_float()
  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }

  /**
   * 将秒转换成可读时间
   * @param int $second
   * @return array
   */
  public static function secondToHuman($second)
  {
    if ($second < 60) {
      return "{$second}秒";

    } else if ($second < 3600) {
      $ii = intval($second / 60);
      return "{$ii}分钟左右";

    } else {

      $hh = intval($second / 3600);
      return "{$hh}小时左右";
    }
  }

  /**
   * 拼接图片静态路径
   */
  public static function staticImgURL($subUrl)
  {
    if (empty($subUrl)) {
      return $subUrl;
    }
    return $_ENV['PRIVATE_IMG_URL'] . '/' . $subUrl;
  }

  /**
   * 获取结果集中的id列表
   * @param array $data 结果集数据
   * @param string $idColumn id字段名称
   */
  public static function getIdArray($data, $idColumn)
  {
    if (empty($data)) {
      return [];
    }

    $idArray = [];
    foreach ($data as $item) {
      if (!in_array($item[$idColumn], $idArray)) {
        array_push($idArray, $item[$idColumn]);
      }
    }
    return $idArray;
  }

  /**
   * 将一个对象结果集添加到另一个结果集中的某一个字段中
   * @param array $result 结果集
   * @param array $objectResult 要添加的对象数据
   * @param string $objectResultIdColumn 对象数据的id字段
   * @param string $relIdColumn 与原结果集中管理的字段名
   * @param string $toColumnName 要添加到的结果集字段
   */
  public static function addObjectToResult($result, $objectResult, $objectResultIdColumn, $relIdColumn, $toColumnName)
  {
    if (!empty($objectResult)) {

      $objectResultMap = [];
      foreach ($objectResult as $item) {
        $objectResultMap[$item[$objectResultIdColumn]] = $item;
      }

      foreach ($result as $i => $item) {
        if (isset($objectResultMap[$item[$relIdColumn]])) {
          $item[$toColumnName] = $objectResultMap[$item[$relIdColumn]];
          $result[$i] = $item;
        }
      }
    }

    return $result;
  }

  /**
   * 将结果集根据某一个字段进行分组
   * @param array $result 结果集
   * @param string $objectResultIdColumn 对象数据的id字段
   */
  public static function getResultGroup($result, $groupId)
  {
    $resultMap = [];
    if (!empty($result)) {

      foreach ($result as $i => $item) {
        if (!isset($resultMap[$item[$groupId]])) {
          $resultMap[$item[$groupId]] = [];
        }
        $resultMap[$item[$groupId]][] = $item;
      }
    }

    return $resultMap;
  }

  /**
   * 将结果集根据一个ID转成Map
   * @param array $result 结果集
   * @param string $columnId 对象数据的id字段
   */
  public static function getResultMap($result, $columnId)
  {
    $resultMap = [];
    if (!empty($result)) {

      foreach ($result as $i => $item) {
        $resultMap[$item[$columnId]] = $item;
      }
    }

    return $resultMap;
  }
}