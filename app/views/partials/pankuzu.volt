<?php
    /* パンくずリストロジック部分 */

    /*
     * メニューリスト
     * ここに追加するとパンくず表示できる
     */
    $_menu = array(
        "admin/" => "管理者一覧",
        "admin/new/" => "管理者作成",
        "admin/edit/" => "管理者編集",
        "admin/change/" => "管理者パスワード変更",
        "user/" => "ユーザ一覧",
        "user/new/" => "ユーザ作成",
        "user/edit/" => "ユーザ編集",
        "user/change/" => "ユーザパスワード変更",
        "terminal/" => "端末一覧",
        "terminal/new/" => "端末作成",
        "terminal/edit/" => "端末編集",
        "carrier/" => "キャリア一覧",
        "carrier/new/" => "キャリア作成",
        "carrier/edit/" => "キャリア編集",
        "maker/" => "メーカー一覧",
        "maker/new/" => "メーカー作成",
        "maker/edit/" => "メーカー編集",
        "os/" => "OS一覧",
        "os/new/" => "OS作成",
        "os/edit/" => "OS編集",
        "version/" => "バージョン一覧",
        "version/new/" => "バージョン作成",
        "version/edit/" => "バージョン編集",
        "organization/" => "組織一覧",
        "organization/new/" => "組織作成",
        "organization/edit/" => "組織編集",
    );

    // 現在のページのパス　例）/wa/men/soba/zaru.php
    //$path = $_SERVER["PHP_SELF"];
    // urlの書き換えなどしてる場合は下記のように現在のページのパスを取得
    $parse_url = parse_url($_SERVER["REQUEST_URI"]);
    $path = $parse_url["path"];

    // ディレクトリで区切って配列に格納　例）array( "" , "wa" , "men" , "soba" , "zaru.php" )
    $array_path = explode("/",$path);

    //邪魔なパンくずを削除
    foreach($array_path as $key => $value){
        if(empty($value)){
            unset($array_path[$key]);
        } elseif ($value === 'backend_app') {
            unset($array_path[$key]);
        }
    }

    $tmp1 = "";

    foreach($array_path as $val){
        // 末尾に / をつける　例）null  ⇒ /　例) wa ⇒ wa/
        // 前データと連結　例）/ + wa/ ⇒ /wa/
        $tmp1 .= "{$val}/";
        // 末尾に index.php をつける　例）/wa/ ⇒ /wa/index.php
        $tmp2  = "{$tmp1}";
        // ファイル名が連続する場合は /index.php を削除　例） zaru.php/index.php ⇒ zaru.php
        $tmp2  = str_replace(".php/index.php",".php",$tmp2);
        // パスとページタイトルを配列に格納
        $breadcrumns[$tmp2] = $_menu[$tmp2];
    }
?>

{# ぱんくず出力 #}
<div class="container">
<ol class="breadcrumb pankuzu-position">
    {% set count = 1 %}
    <? foreach($breadcrumns as $key => $val){ ?>
        <? if(count($breadcrumns) == $count){ ?>
            <li class="pankuzu-color">{{ val }}</li>
        <? } elseif($auth_permission != 1 && (preg_match('/edit/', $_SERVER['REQUEST_URI']) || preg_match('/change/', $_SERVER['REQUEST_URI']))){ ?>
            <li class="pankuzu-color">{{ val }}</li>
        <? } else { ?>
            <li><a href="/backend_app/{{ key }}">{{ val }}</a></li>
        <? } ?>
        {% set count += 1 %}
    <?}?>
</ol>
</div>
