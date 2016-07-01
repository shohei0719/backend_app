 $(function() {
      $(".sortable").sortable();
      $(".sortable").disableSelection();
      $("#submit").click(function() {
          var result = $(".sortable").sortable("toArray");
          $("#result").val(result);
          $("form").submit();
      });
  });
  
  function send_edit($value){
     alert("送信してよろしいでしょうか？");
     document.edit.steak.value = form.status.value;
     document.edit.submit();
  }