<?php
/**
 * 用户相关业务
 * @author tytymnty@gmail.com
 * @since 2016-07-18 15:18:00
 */

namespace Growler\Services;

use Jackpopp\GeoDistance\GeoDistance;
use Growler\Libs\Util;
use Growler\Libs\Page;
use Growler\Libs\PageByOaginalRows;
use Growler\Models\UserModel;
use Growler\Models\CustomerModel;
use Growler\Models\CustomerAccountModel;
use Growler\Models\CompanyModel;
use Growler\Models\DriverModel;
use Growler\Models\OnlineDriverModel;
use Growler\Models\DriverAccountModel;
use Growler\Models\CarModel;
use Growler\Models\DistrictModel;

class UserService
{

  /**
   * 获取用户基本信息
   * @param object $db 数据库连接
   * @param int $userId 用户ID
   * @return null | array
   */
  public static function getUserInfo ($db, $userId)
  {
    $user = $db->get(UserModel::$name, '*', 
    [
      'user_id' => $userId
    ]);

    return $user;
  }

    /**
   * 获取用户详细信息
   * @param object $db 数据库连接
   * @param int $userId 用户ID
   * @param enum 'driver' | 'customer'
   * @return null | array
   */
  public static function getUserDetail ($db, $userId, $type)
  {

    $obj = $type == 'driver' ? DriverModel::$name : CustomerModel::$name;

    $result = $db->select(UserModel::$name, 
    [
      '[>]' . $obj => [
        'user_id' => 'user_id'
      ]
    ], 
    '*',
    [
      UserModel::$name . '.user_id' => $userId
    ]);

    if (count($result) == 0) {
      return null;
    }

    return $result[0];
  }

  /**
   * 修改司机的资源状态
   * @param object $db 数据库连接
   * @param array $driver 司机
   * @param string $status 状态 '0'锁定, '1'空闲
   * @param null|array $driver 自定义更新内容
   */
  private static function driverStatusUpdate($db, $driver, $status, $upd = null) 
  {
    $version = $driver['version'];
    $driverId = $driver['user_id'];
    
    if (empty($upd)) {
      $upd = [];
    }
    $upd['is_free'] = $status;
    $upd['version[+]'] = 1;

    $updCount = $db->update(DriverModel::$name, $upd,

    [
      'AND' => [
        'user_id' => $driverId,
        'version' => $version 
      ]
    ]);

    // 同步更新在线司机缓存表
    $db->update(OnlineDriverModel::$name, 

    [
      'is_free' => $status
    ],

    [
      'driver_id' => $driverId
    ]);

    return $updCount;
  }
  /**
   * 占用司机资源
   * @param object $db 数据库连接
   * @param array $driver 司机
   * @param null|array $driver 自定义更新内容
   */
  public static function driverLock($db, $driver, $upd = null) 
  {
    return self::driverStatusUpdate($db, $driver, '0', $upd);
  }

  /**
   * 释放司机资源
   * @param object $db 数据库连接
   * @param array $driver 司机
   * @param null|array $driver 自定义更新内容
   */
  public static function driverUnlock($db, $driver, $upd = null) 
  {
    return self::driverStatusUpdate($db, $driver, '1', $upd);
  }

  /**
   * 释放没有执行某个订单的司机
   * @param object $db
   * @param int $orderId 订单
   * @param int $unFreeeDriverId 排除的司机
   */
  public static function freeUnWorkingDrivers($db, $orderId, $unFreeeDriverId = null)
  {
    $where = [
      'AND' => [
        'is_free'          => '0',
        'current_order_id' => $orderId
      ]
    ];

    if (!empty($unFreeeDriverId)) {
      $where['AND']['user_id[!]'] = $unFreeeDriverId;
    }

    // 将其他未抢单成功的司机置为空闲状态
    $db->update(DriverModel::$name, 
    [
      'is_free'          => '1',
      'current_order_id' => null
    ],

    $where);
  }

  /**
   * 获取附近空闲的司机
   * @param object $db 数据库连接
   * @param float $la 原点纬度
   * @param float $lng 原点经度
   * @param int $radius  范围，千米
   * @param int $count  获取数量
   * @param null|array $notIn 不包含的用户id
   */
  public static function getFreeNearlyDrivers($db, $lat, $lng, $radius = 1, $count = 1, $notIn = null)
  { 
    $userTable = UserModel::$name;
    $driverTable = DriverModel::$name;
    $carTable = CarModel::$name;
    
    // TODO(ty): 使用新的计算公式
    $geoDistance = new GeoDistance('lat', 'lng');
    $result = $geoDistance->scopeWithin($radius, 'km', $lat, $lng);

    $onlineDriverTable = OnlineDriverModel::$name;

    $where = " is_free='1' ";
    $where .= " AND lat BETWEEN {$result['latBetween'][0]} AND {$result['latBetween'][1]} ";
    $where .= " AND lng BETWEEN {$result['lngBetween'][0]} AND {$result['lngBetween'][1]} ";
    $where .= " AND driver_id < 100 ";

    if (!empty($notIn)) {
      $notIn = implode(',', $notIn);
      $where .= ' AND driver_id NOT IN (' . $notIn . ') ';
    }

    $sql = " SELECT *, {$result['column']} FROM {$onlineDriverTable} ";
    $sql .= " WHERE {$where} ";
    $sql .= " HAVING geo_distance <= {$radius} ";
    $sql .= " ORDER BY geo_distance ASC ";
    $sql .= " LIMIT {$count} ";


    $result = $db->query($sql)->fetchAll();
    
    if (!empty($result)) {
      $carIds = Util::getIdArray($result, 'car_id');
      $cars = $db->select(CarModel::$name, ['car_id', 'car_number'], [
        'car_id' => $carIds
      ]);

      $cars = Util::getResultMap($cars, 'car_id');
      foreach ($result as $i => $item) {
        $result[$i]['car_number'] = $cars[$item['car_id']]['car_number'];
      }
    }

    return $result;
  }

  /**
   * 获取司机列表
   */
  public static function getDriverList($db, $where, $cp)
  {
    $where['AND'][UserModel::$name . '.is_driver'] = '1';

    $page = new PageByOaginalRows($cp);
    $where['LIMIT'] = $page->getLimit();

    $drivers = $db->select(
      UserModel::$name,
      [
        '[>]' . DriverModel::$name   => ['user_id' => 'user_id'],
        '[>]' . CarModel::$name      => ['user_id' => 'driver_id'],
        '[>]' . DistrictModel::$name => [DriverModel::$name . '.district_id' => 'district_id'],
        '[>]' . CompanyModel::$name => [DriverModel::$name . '.company_id' => 'company_id']
      ],

      [
        UserModel::$name . '.*',
        DriverModel::$name . '.*',
        CarModel::$name . '.*',
        DistrictModel::$name . '.name(city_name)',
        CompanyModel::$name . '.name(company_name)'
      ],

      $where
    );

    $drivers = $page->realResult($drivers);

    if (!empty($drivers)) {
      foreach ($drivers as $i => $driver) {
        $drivers[$i]['register_status_name'] = self::driverRegisterStatusName($driver['register_status']);
      }
    }
    return [$drivers, $page];
  }

  /**
   * 获取一个司机的信息
   */
  public static function getDriver($db, $where) 
  {
    $where['AND'][UserModel::$name . '.is_driver'] = '1';

    $where['LIMIT'] = 1;

    $drivers = $db->select(
      UserModel::$name,
      [
        '[>]' . DriverModel::$name => ['user_id' => 'user_id'],
        '[>]' . CarModel::$name    => ['user_id' => 'driver_id'],
        '[>]' . DistrictModel::$name => [DriverModel::$name . '.district_id' => 'district_id'],
        '[>]' . CompanyModel::$name => [DriverModel::$name . '.company_id' => 'company_id']
      ],

      [
        UserModel::$name . '.*',
        DriverModel::$name . '.*',
        CarModel::$name . '.*',
        DistrictModel::$name . '.name(city_name)',
        CompanyModel::$name . '.name(company_name)'
      ],

      $where
    );

    if (empty($drivers)) {
      return null;
    }

    $driver = $drivers[0];
    $driver['register_status_name'] = self::driverRegisterStatusName($driver['register_status']);

    return $driver;
  }


  /**
   * 获取乘客列表
   */
  public static function getCustomerList($db, $where, $cp)
  {
    $page = new PageByOaginalRows($cp);
    $where['LIMIT'] = $page->getLimit();

    $customers = $db->select(
      UserModel::$name,
      [
        '[>]' . CustomerModel::$name   => ['user_id' => 'user_id'],
        '[>]' . CustomerAccountModel::$name => ['user_id' => 'user_id']
      ],

      [
        UserModel::$name . '.*',
        CustomerModel::$name . '.*',
        CustomerAccountModel::$name . '.*'
      ],

      $where
    );

    $customers = $page->realResult($customers);

    return [$customers, $page];
  }

  /**
   * 获取一个乘客的信息
   */
  public static function getCustomer($db, $where) 
  {
    $where['AND'][UserModel::$name . '.is_driver'] = '1';

    $where['LIMIT'] = 1;

    $drivers = $db->select(
      UserModel::$name,
      [
        '[>]' . DriverModel::$name => ['user_id' => 'user_id'],
        '[>]' . CarModel::$name    => ['user_id' => 'driver_id'],
        '[>]' . DistrictModel::$name => [DriverModel::$name . '.district_id' => 'district_id'],
        '[>]' . CompanyModel::$name => [DriverModel::$name . '.company_id' => 'company_id']
      ],

      [
        UserModel::$name . '.*',
        DriverModel::$name . '.*',
        CarModel::$name . '.*',
        DistrictModel::$name . '.name(city_name)',
        CompanyModel::$name . '.name(company_name)'
      ],

      $where
    );

    if (empty($drivers)) {
      return null;
    }

    $driver = $drivers[0];
    $driver['register_status_name'] = self::driverRegisterStatusName($driver['register_status']);

    return $driver;
  }

  /**
   * 获取司机注册状态名称
   */
  public static function driverRegisterStatusName($status) 
  {
    switch ($status) {
      case '0':
        return '等待审核';

      case '1':
        return '审核通过';

      case '-1':
        return '审核失败';
    }
  }

  /**
   * 获取所有注册状态
   */
  public static function getRegisterStatus() 
  {
    return [
      ['key' => '-1', 'name' => '审核失败'],
      ['key' => '0', 'name'  => '等待审核'],
      ['key' => '1', 'name'  => '审核通过']
    ];
  }

  /**
   * 获取司机账户信息
   * @param object $db  数据库连接
   * @param int $userId 所属用户ID
   */
  public static function getDriverAccount($db, $userId)
  {
    return $db->get(DriverAccountModel::$name, '*', [
      'user_id' => $userId
    ]);
  }
}