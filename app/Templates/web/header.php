<!DOCTYPE html>
<html id="<?=$pageId?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=$title?> | <?=$_ENV['SITENAME']?> <?=$_ENV['DOMAIN']?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?=WEB_URL_ROOT?>/assets/libs/AdminLTE/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=WEB_URL_ROOT?>/assets/libs/font-awesome/css/font-awesome.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?=WEB_URL_ROOT?>/assets/libs/AdminLTE/dist/css/AdminLTE.min.css">

  <link rel="stylesheet" href="<?=WEB_URL_ROOT?>/assets/libs/AdminLTE/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?=WEB_URL_ROOT?>/assets/css/all.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="<?=WEB_URL_ROOT?>/assets/libs/html5shiv/dist/html5shiv.min.js"></script>
  <script src="<?=WEB_URL_ROOT?>/assets/libs/respond/dest/respond.min.js"></script>
  <![endif]-->
  <?php
    if (!empty($customHeaderJS)) {
      echo $customHeaderJS;
    }
  ?>
</head>
<?php
  if ($pageId == 'signin') {
?>
  <body class="hold-transition login-page">
<?php
  } else if ($pageId == 'signup_code_validate' || 
    $pageId == 'forget_pwd' ||
    $pageId == 'reset_pwd' 
    ) {
?>
  <body class="hold-transition lockscreen">
<?php
  } else {
?>
  <body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    <?php require_once 'header-inside.php';?>

    <!-- menus -->
    <?php require_once $menus;?>
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
<?php
  }
?>