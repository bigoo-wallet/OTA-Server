<?php
/**
 * 对Medoo进行增强
 * @author tytymnty@gmail.com
 * @since 2016-04-01 17:19:40
 */

namespace Growler\Libs;

class SuperMedoo extends \medoo 
{
  public function beginTransaction() 
  {
    $this->pdo->beginTransaction();   
  }

  public function commit()
  {
    $this->pdo->commit();
  }

  public function rollBack()
  {
    $this->pdo->rollBack();
  }
}