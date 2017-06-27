<?php
session_start();
include_once 'dbconnect.php';
$err_msg = [];

if( isset($_SESSION['id']) != "") {
  // ログイン済みの場合はリダイレクト
  header("Location: main.php");
}

// ログインボタンで下記を実行
if(isset($_POST['login'])) {

  //ユーザ名の入力チェック
  if (empty($_POST['user_id'])) {
    $err_msg[] = "ユーザ名が入力されていません。";
  } else if (empty($_POST['pass'])) {
    $err_msg[] = "パスワードが入力されていません。";
  }
  if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_POST['user_id'];
  $password = $_POST['pass'];


  try{
  // SQL文を作成　参照する
  $sql = "SELECT user_id,password
          FROM member  WHERE user_id = '$user_id' AND password = '$password'";

  // SQL文を実行する準備
  $stmt = $dbh->prepare($sql);
  // SQLを実行
  $stmt->execute();
  // レコードの取得
  $rows = $stmt->fetchAll();
  
  if($rows == array()){
    $err_msg[] = 'ユーザー名とパスワードが一致しません。';
  }else{
      $_SESSION["id"] = $user_id;
      
      //ログイン成功
      header('Location: main.php');
      exit();

  }
      
  
}catch (PDOException $e) {
// 接続失敗した場合
$err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
}
}
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset = "utf-8">
  <title>ログイン画面</title>
 <style>

 body{
   width:1000px;
   text-align:center;
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
 .topname{
   font-size: 30px;

 }
 .cart{
   width:100px;
   text-align:center;
 }
 .member{
   width:100px;
   text-align:center;
 }
 .move{
   margin-top:20px;
 }
 .form{
   margin-top:10px;
   font-size:15px;
 }
 input[type="submit"]{
      background-color:#EEEEEE;
      border: 1px solid blue;
      cursor:pointer;
    }
  .login{
    margin-top:10px;
  }
 </style>

</head>

<body>
  <header>
    <div class="pege_top">
      <h1><a href="main.php">pa de chat</a></h1>
    </div>
  </header>
  <?php foreach($err_msg as $value){ ?>
    <p><?php print $value;?></p>
  <?php } ?>
  <form method="post" action="">
    <h2>ログイン画面</h1>
    <div class="form">
      <input type="text" class="form_group" name="user_id" size="20" style="font-size:20px;"placeholder="ユーザー名">
    </div>

    <div class="form">
      <input type="password" class="form_group" name="pass" size="20"style="font-size:20px;"placeholder="パスワード">
    </div>
    <div class="login"><input type="submit" name="login" value="ログインする"></div>
  </form>

  <div class="move">
    <form method="post">
      <a href="entry.php">新規会員登録はこちら</a>
  </div>

  </body>
  </hatml>

<?php
//var_dump($user_id);
//var_dump($password);
//var_dump($sql);
//var_dump($rows);
?>