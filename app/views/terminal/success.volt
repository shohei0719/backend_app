<?php
if(preg_match('/new/', $_SERVER['REQUEST_URI'])){
  $page_nm = '作成';
}
else if(preg_match('/delete/', $_SERVER['REQUEST_URI'])){
  $page_nm = '削除';
}
else{
  $page_nm = '編集';
}
?>

<h1>完了画面</h1>

<div class="jumbotron">
    <p>端末情報の{{ page_nm }}が完了しました。</p>
    <p><a href="/backend_app/terminal/" class="btn btn-primary btn-large btn-success">端末一覧へ »</a></p>
</div>
