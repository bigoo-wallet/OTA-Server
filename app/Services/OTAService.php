<?php
/**
 * OTA升级相关
 * @author tytymnty@gmail.com
 * @since 2016-09-15 15:51:28
 */

namespace Growler\Services;

use Growler\Models\OTAModel;
use Growler\Models\OTAPlatformModel;
use Growler\Models\OTAChannelModel;
use Growler\Models\OTAIdentificationModel;

class OTAService
{

  /**
   * 获取城市列表
   */
  public static function getOTA($db, $where) 
  {
    if (empty($where)) {
      $where = [];
    }

    $where['LIMIT'] = [0, 1];

    $otas = $db->select(OTAModel::$name,
      [
        '[>]' . OTAPlatformModel::$name       => ['ota_platform_id' => 'ota_platform_id'],
        '[>]' . OTAChannelModel::$name        => ['ota_channel_id' => 'ota_channel_id'],
        '[>]' . OTAIdentificationModel::$name => ['ota_identification_id' => 'ota_identification_id']
      ],

      [
        OTAModel::$name . '.*',
        OTAPlatformModel::$name . '.name(platform_name)',
        OTAChannelModel::$name . '.name(channel_name)',
        OTAIdentificationModel::$name . '.name(identification_name)'
      ],

      $where
    );

    return empty($otas) ? null : $otas[0];
  }
}