<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">  
    <ul class="sidebar-menu">

      <li class="header">OTA管理</li>
      <li class="<?php if ($pageId == 'ota' && $currMethod == 'ota') echo 'active'?>"><a href="<?=WEB_URL_ROOT?>/admin/ota"><i class="fa fa-users"></i> <span>全部</span></a></li>
      <li class="<?php if ($pageId == 'ota' && $currMethod == 'add') echo 'active'?>"><a href="<?=WEB_URL_ROOT?>/admin/ota/add"><i class="fa fa-users"></i> <span>添加</span></a></li>

      <li class="header">我的账户</li>
      <li><a href="<?=WEB_URL_ROOT?>/admin/setting/rpwd"><i class="fa fa-circle-o text-yellow"></i> <span>修改密码</span></a></li>
      <li><a href="<?=WEB_URL_ROOT?>/admin/auth/signout"><i class="fa fa-circle-o text-aqua"></i> <span>退出登录</span></a></li>
    </ul>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
  </section>
  <!-- /.sidebar -->
</aside>