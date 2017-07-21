<?php

// DBとの接続
  include_once 'dbconnect.php';

  session_start();

$img_dir    = './img1/';   // アップロードした画像ファイルの保存ディレクトリ
$err_msg =[];

// ログイン状態チェック
if (!isset($_SESSION['id'])) {
  header("Location: logout.php");
    exit;
  }else{
      $login_id = $_SESSION['id'];
      }

//ログインユーザーのカートの中身を呼び出し
      try{
        // SQL文を作成　参照する
          $sql = "SELECT cart.cart_id,
                         cart.user_id,
                         cart.product_id,
                         cart.num,
                         product.price,
                         product.product_name,
                         product.img,
                         product.stock,
                         product.status
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
            $data[] = $row;
          }
          
          //カートの中が空じゃなかったら
          if(count($rows) !== 0){
            
            foreach($data as $value){
            if($value['stock'] < $value['num']){
              $err_msg[] = $value['product_name'].'の商品の在庫より選択した数量が上回ってます';
            }
            if($value['status']===0){
              $err_msg[] = $value['product_name'].'は購入可能な商品ではありません、削除してください';
            }
          }
        
      
      
//価格のトータル
$total=0;
  foreach($data as $value){
     $total=$total+$value['price']*$value['num'];
      }
//価格のトータルの消費税     
$tax= $total*1.08-$total;
      
//価格の個数
$kazu=0;
  foreach($data as $value){
    $kazu = $kazu + $value['num'];
    }
    
    
//数量のアップデート
if(isset($_POST['update'])){
  if(isset($_POST['code'])){
    $product_id = $_POST['code'];
  }
  if(isset($_POST['update_num'])){
    $num = $_POST['update_num'];
  }
  if($num > 0){
    try{
      
      $sql = "UPDATE cart SET num = ? 
              WHERE user_id= ? AND product_id = ?";
              
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1,$num, PDO::PARAM_STR);
            $stmt->bindValue(2,$_SESSION['id'], PDO::PARAM_STR);
            $stmt->bindValue(3,$product_id, PDO::PARAM_STR);
            $stmt->execute();
            
            header('Location: cart.php');
      
    }catch (PDOException $e) {
          // 接続失敗した場合
          $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
         }
   }else{
     $err_msg[]= '数量0は削除を押してください,変更しない場合はそのまま数量の変更を押してください';
   }
}

//カートの商品単一の削除
if(isset($_POST['delete'])){
    if(isset($_POST['select'])){
      $cart_id = $_POST['select'];
    }
      //削除のコード
      try{
        $sql = "DELETE
                from cart
                WHERE cart_id = ?";
                
          // SQL文を実行する準備
          $stmt = $dbh->prepare($sql);
          $stmt->bindValue(1,$cart_id, PDO::PARAM_STR);
          // SQLを実行
          $stmt->execute();
          
          header('Location: cart.php');
　　　　　exit();
          
      }catch (PDOException $e) {
          // 接続失敗した場合
          $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
         }
}

//カート全体の削除
if(isset($_POST['all_delete'])){

      try{
        $sql = "DELETE
                from cart
                WHERE user_id = ?";
                
                var_dump($spl);
          // SQL文を実行する準備
          $stmt = $dbh->prepare($sql);
          $stmt->bindValue(1,$login_id, PDO::PARAM_STR);
          // SQLを実行
          $stmt->execute();
          
           header('Location: cart.php');
　　　　　exit();
          
      }catch (PDOException $e) {
          // 接続失敗した場合
          $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
         }
}

//購入ボタンを押したら
if(isset($_POST['buy'])){
  header('Location: buy.php');
　　　　　exit();
}
}
}catch (PDOException $e) {
          // 接続失敗した場合
          $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
          }//セレクト文のみで完結
      
//delete from cart where user_id = '向坂';
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
    h2{
      text-align:center;
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
      margin-left: 230px;
    }
    .name{
     margin-left: 400px;
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
      text-align:left;
      margin-left:600px;
      font-size: 20px;
    }
    .buy{
      text-align:center;
      margin-left:400px;
    }
    .no{
      margin-left:300px;
    }
    input[type=text]{
      width:30px;
    }
    .err{
      text-align:center;
      margin-top:20px;
      margin-bottom:20px;
    }
    .nocart{
      text-align:center;
      margin-top:200px;
    }
    .nocart1{
      text-align:center;
      margin-top:20px;
      font-size:20px;
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

    <h2>カートの中身</h2>
    <?php foreach($err_msg as $value){ ?>
    <div class="err"><font color="red">修正してください　</font><?php print $value; ?></div>
    <?php } ?>
    <?php if(count($rows) === 0){ ?>
            <div class="nocart">カートの中身がありません</div>
            <div class="nocart1"><a href="main.php">こちらからHOMEへお戻りください</a></div>
      <?php }else{ ?>
    <?php foreach ($data as $value) { ?>
    <div class='product'>
      <div class="photo">
        <img src="<?php print $img_dir . $value['img']; ?>"width="80" height="120">
      </div>
      <div class='name'>
        <div><?php print $value['product_name']; ?></div>
        <div><?php print number_format($value['price']);?>円(税抜)</div>
        <form method ="POST">
        <div>数量：<input type="text"style="border:solid 1px" name='update_num' value= <?php print $value['num'];?>>  　<span class="change"><input type="submit" value="変更"></span></div>
              <input type="hidden" name="update" value="update">
              <input type="hidden" name="code" value ="<?php print $value['product_id'];?>"></form>
        <div>在庫：<?php print $value['stock'];?></div>
      </div>
      <form method ="POST">
      <div class='clear'><input type="submit" name='delete' value='削除する'></div>
      <input type='hidden' name='select' value="<?php print $value['cart_id']; ?>">
      </form>
    </div>
    <div class="line"></div>
    <?php } ?>
    <?php } ?>

<?php if(count($rows) !==0){ ?>
    <div class="pay">
      <div>商品数 : <?php print $kazu;?></div>
        <div class="price">
          <div>価格 : <?php print number_format($total+$tax+100);?>円</div>
          <div>内(tax¥<?php print number_format($tax); ?> 送料¥100)</div>
        </div>
    </div>
    
    <?php if(count($err_msg) ===0){ ?>
      <form method="POST">
        <div class='buy'><input type="submit" name='buy' value='購入する'></div>
      </form>
      <?php } ?>
      <form method="POST"> 
        <div class='no'><input type="submit" name='all_delete' value='カート全てを削除する'></div>
      </form>
      <?php } ?>
</body> 
</html>
<?php 
//var_dump($product_id);
//var_dump($_POST['id']);
//var_dump($_POST['num']);
//var_dump($login_id);
//var_dump($cart_id);
//var_dump($_POST['cart_id']);
?>
