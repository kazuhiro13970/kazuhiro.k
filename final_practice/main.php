<?php

session_start();
// ログイン状態チェック
if (!isset($_SESSION["id"])) {
    header("Location: logout.php");
    exit;
}

// DBとの接続
include_once 'dbconnect.php';

$img_dir    = './img1/';   // アップロードした画像ファイルの保存ディレクトリ





//SELECT文を表示する
try{

   // SQL文を作成　参照する
   $sql = 'SELECT brand_name,brand_id
           FROM brand
           ORDER BY brand.brand_id ';

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

     // SQL文を作成　参照する
     $sql = 'SELECT  product.product_id,
                     product.product_name,
                     product.price,
                     product.img,
                     product.brand_id,
                     product.status,
                     brand.brand_name,
                     product.stock
             FROM product INNER JOIN brand
             ON product.brand_id=brand.brand_id
            ORDER BY product.product_id desc';

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


 }catch (PDOException $e) {
   // 接続失敗した場合
   $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
   }//セレクト文のみで完結
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Pa De Chat</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/10up-sanitize.css/4.1.0/sanitize.min.css">
<style>

  body{
  width: 1200px;
  margin-left: 25px;
  margin-bottom: 40px;
  background-color: white;
  color:black;
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
h1{
    text-align:center;
    font-family:cursive;
    font-size :50px;
}
 .top_menu{
   display: flex;
   justify-content: flex-end;

 }
 .img{
     margin-left: 100px;
 }

 .tag{
   display: flex;
   justify-content: center;
   margin: auto;
 }
.item{
  text-align:left;
  font-weight:bold;
  margin-top:20px;
  border-bottom: solid;
}
.main_menu{
  display: flex;
}

.left_menu{
  margin-right:15px;
}
.left_brand{
  width: 200px;
  text-align:left;
  margin-top:20px;
}

.view_product{
  display: flex;
  flex-wrap: wrap;
}

.product{
  width: 300px;
  height: 340px;
  text-align: center;
  margin-top: 30px;
}


</style>

</head>
<body>

<header>
  <h1><a href="main.php">pa de chat</a></h1>
</header>


  <div class="top_menu">
    <div class="top_menu">
      <div class="log in"style="width:100px; text-align:center;">ID  <?php print$_SESSION['id']; ?></div>
      <div>|</div>
      <div class="log out"style="width:150px; text-align:center;"><a href="logout.php">LOG OUT</a></div>
      <div>|</div>
      <div class="cart"style="width:150px; text-align:center;"><a href="cart.php">カートへ</a></div>
      <div>|</div>
      <div class='home' style="width:150px;text-align:center;"><a href="main.php">HOME</a></div>
    </div>
  </div>

  <div class="main_img">
    <div class="img"><img src="./img1/DAILY.jpg">
  </div>

  <div class="tag">
    <div style=width:100px><a href="main.php">HOME</a></div>
  </div>


<div class="main_menu">
  <div class="left_menu">
    <div class="item">BRAND LIST</div>
  <?php foreach ($data1 as $value)  { ?>
    <div class="left_brand"><a href="search.php?code=<?php print(htmlspecialchars($value["brand_id"])); ?>"><?php print $value['brand_name'] ; ?></a></div>
    
  <?php } ?>
  </div>

  <div class="right_menu">
    <div class="item">RECCOMEND ITEM</div>
      <div class=view_product>
          
      <?php foreach ($data2 as $value)  { ?>
      <?php if($value['status'] !== 0){ ?>
      <div class="product">
        <div><a href="product_data.php?code=<?php print(htmlspecialchars($value["product_id"])); ?>"><img src="<?php print $img_dir . $value['img']; ?>"width="170" height="220"></div>
        <div class="name"><?php print $value['brand_name']; ?></div>
        <div class="name"><?php print $value['product_name'];?></a></div>
      <?php if($value['stock'] !== 0){  ?>
        <div class="name"><?php print number_format($value['price']);?>円（税抜）</div>
      <?php  }else{ ?>
        <div class="name">品切れ</div>
        <?php } ?>
      </div>
      <?php } ?>
      <?php } ?>
  </div>

</div>


</body>
</html>
