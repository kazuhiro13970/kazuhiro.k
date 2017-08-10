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

if(isset($_GET['code'])){
  $product_id = $_GET['code'];
}

try{
  // leftmenu用　SQL文を作成　参照する
  $sql = 'SELECT brand_name,
                 brand_id
  
          FROM brand
         ORDER BY brand.brand_id';

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
  
  // rightmenu用 SQL文を作成　参照する
  $sql = "SELECT *
          FROM product INNER JOIN brand
          ON product.brand_id=brand.brand_id
          WHERE product.product_id=$product_id";

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
}


// カートに入れるで下記を実行
if(isset($_POST['buy'])) {

  $create_datetime = date('Y-m-d H:i:s');
  
  if (isset($_POST['num'])){
  $number = $_POST['num'];
  }

 if($_POST['status'] === '1'){

  try{
        $sql =  "SELECT cart.user_id,
                        cart.product_id,
                        cart.num
                        FROM cart 
                        WHERE user_id = ? AND product_id = ?"; 
            // SQL文を実行する準備
            $stmt = $dbh->prepare($sql);
            // SQL文のプレースホルダに値をバインド
            $stmt->bindValue(1, $_SESSION['id'], PDO::PARAM_STR);
            $stmt->bindValue(2, $product_id, PDO::PARAM_STR);
             // SQLを実行
            $stmt->execute();
            // レコードの取得
            $rows = $stmt->fetchAll();
            // 1行ずつ結果を配列で取得
            foreach ($rows as $row) {
            $data[] = $row;
           }
            
      }catch (PDOException $e) {
           // 接続失敗した場合
           $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
         }
         
         //カートになかった場合
         if(count($rows) === 0){
           
           try{
            // SQL文を作成
             $sql = 'INSERT INTO cart(user_id,product_id,num,create_datetime) VALUES(?,?,?,?)';
            // SQL文を実行する準備
             $stmt = $dbh->prepare($sql);
            // SQL文のプレースホルダに値をバインド
             $stmt->bindValue(1, $_SESSION['id'], PDO::PARAM_STR);
             $stmt->bindValue(2, $product_id, PDO::PARAM_STR);
             $stmt->bindValue(3, $number, PDO::PARAM_STR);
             $stmt->bindValue(4, $create_datetime, PDO::PARAM_STR);
            // SQLを実行
             $stmt->execute();
             
               header('Location: cart.php');
               exit();
          
            }catch (PDOException $e) {
             // 接続失敗した場合
             $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
           }
            var_dump($err_msg);
         //カートにあった場合
         }else{
          // $rowはたまたま最後の行だっただけなので$data[0]['num']が自然ですかね。
           //メモ　$stock = $row['num']+$number;
           //添え字は'0'ではなく0ですね！
           $stock = $data[0]['num']+$number;
           try{
              $sql = "UPDATE cart
                         SET num = ?
                       WHERE product_id = ?";
                     // SQL文を実行する準備
                       $stmt = $dbh->prepare($sql);
                     // SQL文のプレースホルダに値をバインド
                       $stmt->bindValue(1, $stock, PDO::PARAM_STR);
                       $stmt->bindValue(2, $product_id, PDO::PARAM_STR);
                     //実行
                       $stmt->execute();
                       
                       
                         header('Location: cart.php');
                         exit();
           
              }catch (PDOException $e) {
          // 接続失敗した場合
            $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
          }
         }
}else{
    $err_msg[] = '公開商品ではありません';
}
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Pa De Chat</title>

<style>

  body{
  width: 1200px;
  height:auto;
  margin-left: 25px;
  margin-bottom: 40px;
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
 .top_menu{
   display: flex;
   justify-content: flex-end;

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

.right_menu{

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
.name_product{
  text-align: left;
  font-weight: bold;
  margin:20px;
  font-size: 30px;
}
.product_img{
    display:flex;
    margin-left:20px;
}
.product_img1{
    margin-left:30px;
}
.product_img2{
    margin-left:50px;
}

.price_product{
  margin: 20px 0px 30px 20px;
  font-size:20px;
  border-bottom: solid;
}
.bottom_menu{
    margin-left:20px;
}
.ng{
    text-align:center;
    margin-top: 200px;
    margin-left: 200px;
}
.comment{
    margin-top:20px;
    margin-left:20px;
}
.color{
    margin-top:30px;
}

</style>

</head>
<body>

<header>
  <h1><a href="main.php">pa de chat</a></h1>
</header>


  <div class="top_menu">
      <div class="log in"style="width:100px; text-align:center;">ID  <?php print$_SESSION['id']; ?></div>
      <div>|</div>
      <div class="log out"style="width:150px; text-align:center;"><a href="logout.php">LOG OUT</a></div>
      <div>|</div>
      <div class="cart"style="width:150px; text-align:center;"><a href="cart.php">カートへ</a></div>
      <div>|</div>
      <div class='home' style="width:150px;text-align:center;"><a href="main.php">HOME</a></div>
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
    <?php foreach ($data2 as $value)  { ?>
      <?php } ?>
      <?php if($value['status'] ===1 ){ ?>
    <div class="name_product"><span><?php print $value['brand_name'] ?></span><span>   <?php print $value['product_name']; ?></span></div>
    <div class="product_img">
      <div class="product_img1"><img src="<?php print $img_dir . $value['img']; ?>"width="400" height="550"></div>
      <div class="product_img2"><img src="<?php print $img_dir . $value['img2']; ?>"width="400" height="550"></div>
    </div>
    <div class="price_product"><?php print number_format($value['price']);?>円（税抜）</div>
    
    <form method="post">
    <div class="bottom_menu">
      <div>現在の在庫数：<?php if($value['stock'] =="" || $value['stock']===0){ ?>
                         <?php  print "品切れ"."</div>"; ?>
                         <?php }else{ ?>
                         <?php  print $value['stock']."</div>" ; ?>
                     
      <div class="stock">  
        <select name="num">
          <?php  for($num=1;$num<=$value["stock"];$num++){ ?>
          <?php     print "<option value=$num>$num</option>";  ?>
          <?php } ?>
        </select>
        <?php } ?>
      </div>
      <div><?php if(($value['stock'] =="" || $value['stock']===0) !==TRUE){ ?>
          <?php  print '<input type="submit"  name="buy" value="カートに追加">'; ?>
          <?php } ?>
        <input type="hidden" name='product_id' value="<?php echo $product_id; ?>">
        <input type="hidden" name='status' value="<?php echo $value['status']; ?>">
    </form>
      <div class="comment"><?php print $value['product_comment']; ?></div>
      <div class="color">color&nbsp&nbsp&nbsp<?php print $value['color']; ?></div>
      <div class="size">size&nbsp&nbsp&nbsp&nbsp&nbsp<?php print $value['size']; ?></div>
      </div>
     <?php }else{ ?>
      <div class="ng">公開商品ではありません</div>
     <?php } ?>

</div>
</body>
</html> 
