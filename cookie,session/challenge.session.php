<?php 
  session_start();

    $now = date('Y/m/d H:i:s');
?>    
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Session</title>
  </head>
  <body>
    <?php
    if(!isset($_SESSION['count'])){
        print ("初回のアクセスです<br>");
        print $now.("現在日時");
        //countに1を初回として時刻を保存
        $_SESSION['count'] =1;
        $_SESSION['zikoku'] = $now;
        
    }else{
        //保存しているcountを関数にする
        $count= $_SESSION['count'];
        //1ずつ＋
        $count++;
        
        print ("合計".$count."回目のアクセスです<br>");
        $_SESSION['count'] = $count;
        
        print $now."<br>";
        //保存しているzikokuがある場合呼び出し
        if(isset($_SESSION['zikoku'])){
            print ($_SESSION['zikoku'].'前回のアクセス日時');
        }
        //現在時刻をzikokuにいれSESSIONに保存
        $_SESSION['zikoku'] = $now;

    }
   
    ?>
  </body>
</html>