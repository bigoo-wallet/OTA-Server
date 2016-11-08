<?php
/*
 * 分页辅助, 需要先得到具体的数据行数
 * @author tytymnty@gmail.com
 * @since 2016-04-04 23:21:41
 **/

namespace Growler\Libs;

class Page
{
  /*每页显示多少条记录*/
  private $paginalRows;
  
  /*每次显示多少分页（分页步长）*/
  private $pageStep;
  
  /*数据的总行数*/
  private $totalRows;
  
  /*数据的总页数*/
  private $totalPages;
  
  /*当前是第几页*/
  private $currentPage;
  
  /*前一页*/
  private $frontPage;
  
  /*后一页*/
  private $nextPage;
  
  /*开始显示的页码数*/
  private $pageStepBegin;
  
  /*结束显示的页码数*/
  private $pageStepEnd;
  
  /*当前行*/
  private $currentRow;
  
  /*首页*/
  private $mainPage;
  
  /*尾页*/
  private $lastPage;
  
  /**分页的列表**/
  private $pages ;
  
  /**sql limit 字符串**/
  private $limit;

  private $linkString = NULL;
  /**
   * 封装查询字符串
   * **/
  private function mkLinkString () 
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $this->queryArray = $_POST;
    } else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      $this->queryArray = $_GET;
    }
    $linkString = '?';
    $queryArray = $this->queryArray;
    
    if (count($queryArray)>0) {
      foreach ($queryArray as $key=>$value) {
        if ($key != 'cp' && $key != 'method') {
          $linkString .= $key.'='. $value;
          $linkString .= '&';
        }
      }
      $linkString = substr($linkString , 0 , strrpos($linkString,'&'));
    }
    if (empty($linkString)) {
      $linkString ='?';
    } else {
      $linkString .= '&';
    }
    $this->linkString = $linkString;
  }

  /*获取连接字符串*/
  public function getLinkString($noparams=NULL)
  {
    $this->mkLinkString();
    if (!empty($noparams) && !empty($this->linkString)) {
      $linkString = preg_replace('/\?/', '', $this->linkString);
      $arr = explode('&', $linkString);
      $nparams = '';
      foreach ($arr as $i=>$item) {
        $tmp = preg_split('/\=/', $item);
        list($key , $value) = $tmp;
        if (!empty($item)) {
          if (in_array($key, $noparams)) {
            continue;
          } else {
            $nparams.= "{$key}={$value}&";
          }
        }
      }
      if (empty($nparams)) {
        $this->linkString='?';
      } else {
        $this->linkString="?{$nparams}";
      }
    }
    $this->linkString = preg_replace('/</', '', $this->linkString);
    $this->linkString = preg_replace('/>/', '', $this->linkString);
    return $this->linkString;
  }

  /*
    构造函数
    @param $currentPage 当前页码数
    @param $paginalRows 每页显示多少行
    @param $pageStep 分页步长
  */
  public function __construct($currentPage , $totalRows)
  {
    $this->currentPage = $currentPage;
    $this->paginalRows = intval($_ENV['PAGE_RESULT_COUNT']);
    $this->pageStep = intval($_ENV['PAGE_STEP']);
    $this->setTotalRows($totalRows);
  }
  
  /*
    设置总行数
    @param $totalRows : 数据总行数
  */
  private function setTotalRows($totalRows)
  {
    $this->totalRows = $totalRows;
    $paginalRows = $this->paginalRows;
    
    //根据数据总行数 和 每页显示数量获取总页数
    if ($paginalRows >= 1) {
      $totalPages = (integer)(($totalRows + $paginalRows-1)/$paginalRows);
      $this->totalPages = $totalPages;
    }
    #-1表示为最后一页
    if ($this->currentPage == -1) {
      $this->currentPage=$this->totalPages;
    }
    $this->setPageStep();
    $this->setCurrentPage($this->currentPage);
    $this->setCurrentRow();
    $this->setLastPage();
    $this->setLimit();
    $this->pages = array();
    for ($i=$this->pageStepBegin; $i<=$this->pageStepEnd; $i++) {
      $this->pages[]=$i;
    }
  }
  
  /*设置、获取分页显示步长*/
  private function setPageStep()
  {
      
    //显示步长
    $pageStep = $this->getPageStep();
    
    //总页数
    $totalPages = $this->getTotalPages();
    
    //当前页
    $currentPage = $this->getCurrentPage();
    
    //如果总页数 <= 显示步长
    if ($totalPages <= $pageStep) {
        
      $this->pageStepBegin = 1;
      $this->pageStepEnd = $this->getTotalPages();
        
    } else if($currentPage < ($pageStep/2+1)) {
      //如果当前页 < 步长的1/2
      $this->pageStepBegin = 1;
      $this->pageStepEnd = $pageStep;
        
    } else {
      //如果当前页 > 步长的1/2
      $this->pageStepBegin = $currentPage-floor($pageStep/2);
      $this->pageStepEnd = $currentPage+floor($pageStep/2);
        
        
      //如果显示的最后一页 >=总页码数
      //那么显示的页码为：(总页数 - 步长) 至 总页数
      if ($this->pageStepEnd >= $totalPages) {
          
        $this->pageStepBegin = $totalPages-$pageStep + 1;
        $this->pageStepEnd = $totalPages;
      }
    }
  }
  
  /*设置、获取当前页*/
  private function setCurrentPage($currentPage)
  {
      
    //如果当前页超过范围
    if($currentPage<1 || $currentPage> $this->getTotalPages())
      $this->currentPage = 1;
    else
      $this->currentPage = $currentPage;
    
    //设置分页显示范围
    $this->setPageStep();
    //设置上一页
    $this->setFrontPage($this->currentPage-1);
    
    //设置下一页
    $this->setNextPage($this->currentPage+1);
    
    $this->setMainPage();
  }

  /*设置、获取前一页*/
  private function setFrontPage($frontPage)
  {
    if ($frontPage < 1 || $frontPage > $this->getTotalPages())
      $this->frontPage=1;
    else
      $this->frontPage=$frontPage;
  }

  /*设置、获取下一页*/
  private function setNextPage($nextPage)
  {
    if($nextPage < 1 || $nextPage > $this->getTotalPages())
      $this->nextPage=$this->getTotalPages();
    else
      $this->nextPage=$nextPage;
      
  }

  /*获取首页*/
  private function setMainPage()
  {
    $this->mainPage = 1;

    return $this->mainPage;
  }
  
  /*获取尾页*/
  private function setLastPage()
  {
    $this->lastPage = $this->getTotalPages();
    return $this->lastPage;
  }

  /*获取当前行*/
  private function setCurrentRow()
  {
    $this->currentRow = ($this->currentPage-1) * $this->getPaginalRows();
    return $this->currentRow;
  }
  
  /**
  *   获取limit
  **/
  private function setLimit()
  {
    $this->limit = [$this->currentRow, $this->paginalRows];
  }

  public function getPageStep()
  {
    return $this->pageStep;
  }
  
  /*获取每页显示数据行数*/
  public function getPaginalRows()
  {
    return $this->paginalRows;
  }
  
  /*获取总行数*/
  public function getTotalRows()
  {
    return $this->totalRows;
  }
  
  /*获取总页数*/
  public function getTotalPages()
  {
    return $this->totalPages;
  }
  
  public function getCurrentPage()
  {
    return $this->currentPage;
  }
  
  /**获取当前显示的分页列表**/
  public function getPages()
  {
    return $this->pages;
  }
  
  public function getMainPage()
  {
    return $this->mainPage;
  }
  
  public function getLastPage()
  {
    return $this->lastPage;
  }
  
  public function getFrontPage()
  {
    return $this->frontPage;
  }
  
  public function getNextPage()
  {
    return $this->nextPage;
  }
  
  /*获取开始显示的页码数*/
  public function getPageStepBegin()
  {
    return $this->pageStepBegin;
  }
  
  /*获取结束显示的页码数*/
  public function getPageStepEnd()
  {
    return $this->pageStepEnd;
  }
  
  public function getLimit($params=NULL)
  {
    return $this->limit;
  }
  
  public function info()
  {
    $vars = get_object_vars($this);
    foreach ($vars as $key => $val){
      echo $key .' : '. print_r($val,true).'<br/>';
    }
  }
}