<?php

// DBとの接続
  include_once 'dbconnect.php';

  session_start();

$img_dir    = './img1/';   // アップロードした画像ファイルの保存ディレクトリ

// ログイン状態チェック
if (!isset($_SESSION['id'])) {
  header("Location: logout.php");
    exit;
  }else{
      $login_id = $_SESSION['id'];
      }
      
      try{
          
      //ログインユーザーの情報呼び出し
          $sql= "SELECT * from member WHERE user_id='$login_id'";
          
           // SQL文を実行する準備
          $stmt = $dbh->prepare($sql);

          // SQLを実行
          $stmt->execute();
          // レコードの取得
          $rows = $stmt->fetchAll();
          // 1行ずつ結果を配列で取得
          foreach ($rows as $row) {
            $data1[] = $row;
          }

//ログインユーザーのカートの中身を呼び出し
        // SQL文を作成　参照する
          $sql = "SELECT cart.cart_id,
                         cart.user_id,
                         cart.product_id,
                         cart.num,
                         product.price,
                         product.product_name,
                         product.img,
                         product.stock
                         FROM cart INNER JOIN product ON cart.product_id = product.product_id
                         WHERE cart.user_id = '$login_id'"; 
                         
                         //var_dump($sql);チェック用

          // SQL文を実行する準備
          $stmt = $dbh->prepare($sql);

          // SQLを実行
          $stmt->execute();
          // レコードの取得
          $rows = $stmt->fetchAll();
          // 1行ずつ結果を配列で取得
          foreach ($rows as $row) {
            $data2[] = $row;
          }
          
          if(count($rows) !== 0){
      
        
          
//価格のトータル
$total=0;
  foreach($data2 as $value){
     $total=$total+$value['price']*$value['num'];
      }
//価格のトータルの消費税     
$tax= $total*1.08-$total;
      
//価格の個数
$kazu=0;
  foreach($data2 as $value){
    $kazu = $kazu + $value['num'];
    }
}
    
      }catch (PDOException $e) {
          // 接続失敗した場合
          }//セレクト文のみで完結
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset = "utf-8">
    <title>カートの中身</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/10up-sanitize.css/4.1.0/sanitize.min.css">
    <style>
    body{
      width: 1000px;
      margin-left: 25px;
      margin-bottom:30px;
     
    }
    h1{
    text-align:center;
    font-family:cursive;
    font-size :50px;
    }
    h2{
      text-align:center;
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
    .top_menu{
     display: flex;
     justify-content: flex-end;

    }
    .product{
      height: 200px;
    }
    .photo{
      float: left;
      margin-left: 300px;
    }
    .name{
     margin-left: 500px;
     padding-top: 20px;
    }
    .clear{
      float: right;
      margin-right: 180px;
      margin-top:-60px;
    }
    .line{
      float: left;
      width:650px;
      border:solid 1px;
      margin-top: -60px;
      margin-left: 200px;
    }
    .pay{
      text-align:center;
      margin-top: 20px;
      font-size: 20px;
    }
    .buy{
      text-align:center;
      margin-top:30px;
      font-size:30px;
    }
    .no{
      margin-left:300px;
    }
    .user{
        text-align:left;
        margin-left:300px;
        font-size: 20px;
    }
    .tyeck{
        text-align:center;
        margin-top:20px;
    }
    .cancel{
        text-align:center;
        margin-top: 40px;
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
      <h1><a href="main.php">pa de chat</a></h1>
    </header>
    <div class="top_menu">
      <div class="log in"style="width:100px; text-align:center;">ID:<?php print$_SESSION['id']; ?></div>
      <div>|</div>
      <div class="log out"style="width:150px; text-align:center;"><a href="logout.php">LOG OUT</a></div>
      <div>|</div>
      <div class='home' style="width:150px;text-align:center;"><a href="main.php">HOME</a></div>
    </div>

    <h2>購入確認画面</h2>
    
    <?php if(count($rows) !== 0){ ?>
    <?php foreach ($data2 as $value) { ?>
    <div class='product'>
      <div class="photo">
        <img src="<?php print $img_dir . $value['img']; ?>"width="80" height="120">
      </div>
      <div class='name'>
        <div><?php print $value['product_name']; ?></div>
        <div><?php print number_format($value['price']);?>円(税抜)</div>
        <div>数量：<?php print $value['num']; ?></div>
        <div>在庫：<?php print $value['stock'];?></div>
      </div>
    </div>
    <div class="line"></div>
    <?php } ?>

    <div class="">
        <?php foreach ($data1 as $value)  { ?>
      <?php } ?>
      <div class="user">
      <div>お客様情報</div>
      <div>お名前　　<?php print $value['hull_name']; ?></div>
      <div>〒　　　　<?php print $value['address1'].'-'.$value['address2']; ?></div>
      <div>住所　　　<?php print $value['address3']; ?></div>
      <div>　　　　　<?php print $value['address4']; ?></div>
      <div>　　　　　<?php print $value['address5']; ?></div>
      </div>
      
      
    <div class="pay">
      <div>商品数 : <?php print $kazu;?></div>
        <div class="price">
          <div>支払合計 : <?php print number_format($total+$tax+100);?>円</div>
          <div>内(tax¥<?php print number_format($tax); ?> 送料¥100)</div>
        </div>
    </div>
    
      <div class="tyeck"><font color="red">※</font>上記にお間違いなければ購入するを押してください</div>
    
      <form action="result.php" method="POST">
        <div class='buy'><input type="submit" name='all_buy' value='購入する'></div>
        <input type="hidden" name="buy" value='1'>
        <input type="hidden" name="total" value="<?php print $total; ?>">
      </form>
      
      <div class="cancel"><a href="cart.php">訂正の場合はこちらを押してください</a></div>
    <?php }else{ ?>
     <div class="err">カートの中身はありません</div>
    <?php } ?>
</body>
</html>