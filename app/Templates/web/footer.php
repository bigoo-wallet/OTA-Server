<?php
  if ($pageId != 'signin' &&
      $pageId != 'signup_code_validate' &&
      $pageId != 'forget_pwd' &&
      $pageId != 'reset_pwd' ) {
?>
    </div>

  </div>
  <!-- ./wrapper -->

  <footer class="main-footer">
    <?php require TEMP_ROOT. '/web/copyright.php';?>
  </footer>

<?php
  }
?>

<!-- jQuery 2.2.0 -->
<script src="<?=WEB_URL_ROOT?>/assets/libs/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?=WEB_URL_ROOT?>/assets/libs/AdminLTE/bootstrap/js/bootstrap.min.js"></script>

<script src="<?=WEB_URL_ROOT?>/assets/libs/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?=WEB_URL_ROOT?>/assets/libs/AdminLTE/plugins/fastclick/fastclick.js"></script>

<?php
  if (isset($libs)) {
    echo $libs;
  }
?>
<!-- AdminLTE App -->
<script src="<?=WEB_URL_ROOT?>/assets/libs/AdminLTE/dist/js/app.min.js"></script>
<script src="<?=WEB_URL_ROOT?>/assets/js/module/base.js"></script>
<?php
  if (isset($js)) {
    echo $js;
  }
?>

</body>
</html>