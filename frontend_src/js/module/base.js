"use strict"

String.prototype.replaceAll = function(target, replacement) {
  return this.split(target).join(replacement);
};

/**
 * 按钮确认
 */
 $(function () {
  $(".btn-confirm").click(function () {
    var msg = $(this).attr("confirm-msg");
    if (!msg) {
      msg = "确定要执行此操作吗？";
    }
    return confirm(msg);
  });
 });

/**
 * 表单提交
 */
var formSubmit = function() {
  // 阻止回车自动提交
  $("form input").keypress(function(event) {
    if (event.which == 13) {
      event.preventDefault();
      return false;
    }
  });

  var ajaxTimerId = 0;
  $(".btn-submit").click(function() {
    if (!$(this).is(":disabled")) {

      // btn-submit-confirm

      var form = $(this).parents('form'),
          action = form.attr("action"),
          method = form.attr("method"),
          btn = $(this);

      // prevent multiple clicks
      btn.prop("disabled", true);

      var formData = new FormData(form[0]);
      $(form).find("input, select, img, radio, textarea").each(function() {
        if($(this).prop("type").toLowerCase()=='file') {

          formData.append(name, $(this)[0]);
        }
      });

      // Ajax submit function
      var ajaxSubmit = function() {
        if(ajaxTimerId != 0) {
            return false;
        }
        ajaxTimerId = setTimeout(function() {
            clearTimeout(ajaxTimerId);
            ajaxTimerId = 0;
        }, 3000);

        var settings = {
          method: method,
          url: action,
          dataType: "json",
          processData: false,
          contentType: false,
          data: formData
        };

        $.ajax(settings).done(function(resp) {
          // 请求 - 返回成功
          if (resp.code == 0) {
            btn.find(".label-success").removeClass("hide");
            // 需要延迟并显示成功信息
            if (typeof(resp.data.delaySuccess) != "undefined") {
              setTimeout(function () {
                if (typeof(resp.data.href) != "undefined") {
                  window.location.href = resp.data.href;

                } else if (typeof(resp.data.reload) != "undefined") {
                  window.location.reload();
                }
                
                btn.find(".label-success").addClass("hide");
                btn.prop("disabled", false);
              }, 3000);

            } else {

              if (typeof(resp.data.href) != "undefined") {
                window.location.href = resp.data.href;

              } else if (typeof(resp.data.reload) != "undefined") {
                window.location.reload();
              }

              btn.prop("disabled", false);
            }

          } else {
            btn.prop("disabled", false);

            // 显示错误信息
            for (var k in resp.data.errors) {
              var formGroup = form.find("[name='"+k+"']").parents(".form-group");
              displayError(formGroup, resp.data.errors[k][0]['err_msg']);
            }
          }

          (typeof(afterSubmit) != 'undefined' && afterSubmit != null) && afterSubmit(resp);

        }).fail(function(jqXHR, textStatus) {
          btn.prop("disabled", false);

          (typeof(afterSubmit) != 'undefined' && afterSubmit != null) && afterSubmit(resp);
        });
      };

      // execute beforeSubmit
      if (typeof(beforeSubmit) != 'undefined' && beforeSubmit != null) {
        beforeSubmit(function() {
          ajaxSubmit();
        });
      } else {
        ajaxSubmit();
      }
    }
  });

  // remove error message when the input focus
  $("form input,form select,form textarea").focus(function() {
    hideError($(this));
  });

  $("select").change(function() {
    hideError($(this));
  });
};

/**
 * 显示/隐藏表单错误提示
 */
var displayError = function (formGroup, msg) {
  formGroup.addClass("has-error");
  formGroup.find(".help-block").text(msg);
},
hideError = function ($subEle) {
  $subEle.parents(".form-group.has-error").find(".help-block").html("&nbsp;");
  $subEle.parents(".form-group.has-error").removeClass("has-error");
};

/**
 * 模态框
 */
$(function () {
  // 关闭模态框
  $(".modal").click(function (e) {
    if($(e.target.target).hasClass("close") || $(e.target).hasClass("btn-close")) {
      $(".modal-view").addClass("hide");
      $("html").css("overflow-y", "auto");
    }
  });

  // 打开模态框
  $(".btn-modal-raise").click(function () {
    var target = $(this).attr("modal-target");
    $("#"+target).removeClass("hide");
    $("html").css("overflow-y", "hidden");
    $(".modal")
  });
});

$(document).ready(function() {
  formSubmit();
});


$(function () {
  $(".btn-refresh").click(function() {
    window.location.reload();
  });
});