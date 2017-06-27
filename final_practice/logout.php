<?php
session_start();

if(isset($_SESSION["id"])){
    $err_msg = "ログアウトしました。";
}else{
    $err_msg = "セッションがタイムアウトしました。";
}

// セッションの変数のクリア
$_SESSION = array();

// セッションクリア
//@session_destroy();
?>

<!DOCTYPE html>
<html lang="ja">
 <head>
    <meta charset="UTF-8">
    <title>ログアウト</title>
    <style>
    body{
        text-align:center;
        width:1000px;
      }
    h1{
    text-align:center;
    font-family:cursive;
    font-size :50px;
      }
    a {
    text-decoration: none;
    }
    a:link { 
    color: black; 
    }
    a:visited {
    color:black;
    }
    a:hover {
    color: #ff0000; 
    
    }
        </style>
    </head>
    <body>
        <header>
           <h1><a href="main.php">Pa De Chat</a></h1>
        </header>
        <h2>ログアウト画面</h2>
        <div><?php echo htmlspecialchars($err_msg, ENT_QUOTES); ?></div>
        
        <div><a href="login.php">ログイン画面に戻る</a></div>
    </body>
</html>