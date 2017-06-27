<?php 
    $now = date('Y/m/d H:i:s');
    setcookie("zikoku",$now); 
 
  
?>    
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Cookie</title>
  </head>
  <body>
      <div>
    <?php
    // cookieが設定されていなければ(初回アクセス)、cookieを設定する
    if ( !isset($_COOKIE['visit_count']) ) {
        
      // cookieを設定
      setcookie('visit_count', 1, time() + 3600);
      print("初めてのアクセスです<br>");
      print $now.("現在時刻");
    }
    // cookieがすでに設定されていれば(2回目以降のアクセス)、cookieで設定した数値を加算する
    else {
      $count = $_COOKIE['visit_count'] + 1;
      setcookie('visit_count', $count, time() + 3600);
      print ("合計".$count."回目のアクセスです<br>");
      print $now.("現在時刻<br>");
      print $_COOKIE["zikoku"].("前回アクセス日時");
    }
    ?>
    </div>
    
  </body>
</html>