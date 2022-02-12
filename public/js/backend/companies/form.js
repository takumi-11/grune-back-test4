 $('.ajaxzip3').on('click', function(){
    AjaxZip3.zip2addr('postcode','','prefecture_id','city','local');

    //成功時に実行する処理
    AjaxZip3.onSuccess = function() {
        $('.addr3').focus();
    };
    
    //失敗時に実行する処理
    AjaxZip3.onFailure = function() {
        alert('郵便番号に該当する住所が見つかりません');
    };
    
    return false;
});

$(function() {
  // init: side menu for current page
  $("li#menu-companies").addClass("menu-open active");
  $("li#menu-companies")
    .find(".treeview-menu")
    .css("display", "block");
  $("li#menu-companies")
    .find(".treeview-menu")
    .find(".add-companies a")
    .addClass("sub-menu-active");

  $("#company-form").validationEngine("attach", {
    promptPosition: "topLeft",
    scroll: false,
  });

  // init: show tooltip on hover
  $('[data-toggle="tooltip"]').tooltip({
    container: "body",
  });
});

$(function() {
  $("[name='image']").on("change", function(e) {
        if("[name='image']" === null) {
            $('#img-change').html(
                "<p style='color: red; margin-left: 12px;'>画像をアップロードしてください(推奨サイズ:1280px × 720px ・ 容量は5MBまで)</p>" +
                "<img src='/img/no-image/no-image.jpg' width='300px' style='margin-left: 12px;'></img>")
        } else {
            $('#img-change').html(
                "<img id='preview' width='300px' style='margin-left: 12px; margin-top: 10px;'></img>"
            )

            var reader = new FileReader();
        
            reader.onload = function(e) {
              $("#preview").attr("src", e.target.result);
            };
            reader.readAsDataURL(e.target.files[0]);
        }
  });
});