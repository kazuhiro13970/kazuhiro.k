<?php
session_start();
if( isset($_SESSION['id']) != "") {
  // ログイン済みの場合はリダイレクト
  header("Location: main.php");
}
// DBとの接続
include_once 'dbconnect.php';

$err_msg=[];
$rusult_msg=[];

// new_inputがPOSTされたときに下記を実行

if(isset($_POST['new_input'])) {
    $creat_datetime = date('Y-m-d H:i:s');
    $name = $_POST['name'];
    $hull = $_POST['hullname'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $address3 = $_POST['address3'];
    $address4 = $_POST['address4'];
    $address5 = $_POST['address5'];
    $pass = $_POST['pass'];
    $pass1 = $_POST['pass1'];

      if($name == ''){
    $err_msg[] = 'ユーザー名を入力してください。';
  }else if((preg_match('/^([a-zA-Z0-9]{6,20})+$/',$name))!== 1){
    $err_msg[] = 'ユーザー名は半角英数字6文字以上、20文字以内で入力してください。';
  }
  if($hull == ''){
    $err_msg[] = 'お名前を入力してください。';
  }else if(mb_strlen($hull) > 30){
    $err_msg[] = 'お名前は30文字以内で入力してください。';
  }

    //郵便番号1
  if($address1 == ''){
    $err_msg[] = '郵便番号を入力してください';
  }else if((preg_match('/^([0-9]{3})+$/',$address1))!== 1){
    $err_msg[] = '郵便番号はそれぞれ3桁、4桁の半角数字で入力してください。';
  }
   //郵便番号2
  if($address2 == ''){
    $err_msg[] = '郵便番号を入力してください';
  }else if((preg_match('/^([0-9]{4})+$/',$address2))!== 1){
    $err_msg[] = '郵便番号はそれぞれ3桁、4桁の半角数字で入力してください。';
  }

  //都道府県
  if($address3 ==''){
    $err_msg[] ='都道府県を入力してください。';
  }
  //市区町村番地
  if($address3 ==''){
    $err_msg[] ='市区町村、番地を入力してください。';
  }
  if(((preg_match('/^([a-zA-Z0-9]{6,20})+$/',$pass))!== 1)){
    $err_msg[] ='パスワードは半角英数字6文字以上20文字以内で入力してください。';
  }else if($pass !== $pass1){
    $err_msg[] = 'パスワードが一致していません。';
  }

  if(count($err_msg) == 0){
    
    try{
      
      $sql = "SELECT *
              FROM member
              WHERE user_id = ?";
              
         // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $name, PDO::PARAM_STR);
          // SQLを実行
        $stmt->execute();
        // レコードの取得
            $rows = $stmt->fetchAll();
            
        }catch (PDOException $e) {
          // 接続失敗した場合
          $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
        }//セレクト文のみで完結
    
       //メンバーにいなかった場合になかった場合
         if(count($rows) === 0){
           try{
    

                  $sql = 'INSERT INTO member(user_id,hull_name,password,address1,address2,
                                             address3,address4,address5)
                                             VALUES(?,?,?,?,?,?,?,?)';
                        // SQL文を実行する準備
                        $stmt = $dbh->prepare($sql);
                        // SQL文のプレースホルダに値をバインド
                       $stmt->bindValue(1, $name, PDO::PARAM_STR);
                       $stmt->bindValue(2, $hull, PDO::PARAM_STR);
                       $stmt->bindValue(3, $pass, PDO::PARAM_STR);
                       $stmt->bindValue(4, $address1, PDO::PARAM_STR);
                       $stmt->bindValue(5, $address2, PDO::PARAM_STR);
                       $stmt->bindValue(6, $address3, PDO::PARAM_STR);
                       $stmt->bindValue(7, $address4, PDO::PARAM_STR);
                       $stmt->bindValue(8, $address5, PDO::PARAM_STR);
                       // SQLを実行
                       $stmt->execute();

                        $result_msg[] = '登録に成功しました';

                    }catch (PDOException $e) {
                         throw $e;
                        $result_msg[] = '失敗しました';
                     }
       }else{
         $err_msg[] = 'すでにそのIDは使用されています。別のIDでご登録ください';
           }
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset = "utf-8">
  <title>会員登録画面</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/10up-sanitize.css/4.1.0/sanitize.min.css">
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
.center{
    display:flex;
    justify-content:center;
}

.left{
    text-align:right;
}
.right{
    text-align:left;
}
div{
    margin:20px;
}
input{
    border:solid 1px;
}
.ki{
   margin-bottom:40px;
}
input[type="submit"]{
      background-color:#EEEEEE;
      border: 1px solid blue;
      cursor:pointer;
    }
 </style>
</head>

<body>
  <header>
    <div class="pege_top">
      <h1><a href="main.php">Pa De Chat</a></h1>
    </div>
  </header>
  <h2>会員登録画面</h2>
  　<?php if(count($err_msg) !==0){ ?>
    <?php foreach ($err_msg as $value) {?>
  <p><?php print $value;?></p>
  <?php } ?>
  <?php }else if(count($result_msg) !== 0){ ?>
  <?php foreach ($result_msg as $value) {?>
  <p><?php print $value;?></p>
  <?php } ?>
  <?php } ?>
  
  <div class="ki"><font color="red">※</font>は必須項目です</p>
  <div class="center" align="center">
    <div class="left">
      <div>ユーザー名</div>
      <div>お名前</div>
      <div>郵便番号</div>
      <div>都道府県</div>
      <div>市区町村番地</div>
      <div>建物名</div>
      <div>パスワード</div>
      <div>確認 パスワード</div>
    </div>
    
    <div class="right">
        <form action="" method="POST" enctype="multipart/form-data">
        <div><input type="text" name="name" size="35"placeholder="ログイン名を入力してください"><font color="red">※</font></div>
        <div><input type="text" name="hullname" size="35"placeholder="お名前を入力してください"><font color="red">※</font></div>
        <div><input type="text" name="address1" size="3" maxlength="3" value="">
              -<input type="text" name="address2" size="4" maxlength="4"value=""><font color="red">※</font></div>
        <div><input type="text" name="address3" size="35" maxlength="6"value=""><font color="red">※</font></div>
        <div><input type="text" name="address4" size="35" maxlength="255"value=""><font color="red">※</font></div>
        <div><input type="text" name="address5" size="35" maxlength="255"></div>
        <div><input type="password" name="pass" size="35" maxlength="50"placeholder="半角英数字で入力してください"><font color="red">※</font></div>
        <div><input type="password" name="pass1" size="35" maxlength="50"placeholder="半角英数字で入力してください"><font color="red">※</font></div>
    </div>
  </div>
   
  <input type="submit" name="new_input"value="会員登録する">
</form>
  <a href="login.php">ログインはこちら</a>
  </div> 
  
  </body>
  </html> 
