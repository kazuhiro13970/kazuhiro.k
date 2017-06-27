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

$err_msg= [];
$result_msg=[];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $user_id = $_SESSION['id'];
    
    if($_POST['buy'] == 1){
        //　ユーザー名で一致するカートの中身を呼び出し
     try{
           $sql = "SELECT cart.user_id,
                          cart.num, 
                          product.stock,
                          product.product_id,
                          product.product_name,
                          product.img,
                          product.price,
                          product.status
                          FROM cart  INNER JOIN product
                          ON cart.product_id = product.product_id
                          WHERE user_id = ?";
                    //var_dump($sql);
            // SQL文を実行する準備
              $stmt = $dbh->prepare($sql);
              $stmt->bindValue(1, $user_id, PDO::PARAM_STR);
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
               }
              //var_dump($data);
              
              
              // トランザクション開始
              $dbh->beginTransaction();
             //カートになかった場合
           if(count($rows) === 0){
              $err_msg[] = 'カートの中身はありません';
              }else{
                
              //データ毎にストックから個数を引く
            foreach($data as $value){
                if($value['stock'] >= $value['num']){
                    $product_id = $value['product_id'];
                    $stock = $value['stock']-$value['num'];
                    
                    if($value['status'] !==0){
                    
    
                    try{
                          $sql = "UPDATE product
                                  SET stock = ?
                                  WHERE product_id = ?";
                           // SQL文を実行する準備
                           $stmt = $dbh->prepare($sql);
                           // SQL文のプレースホルダに値をバインド
                           $stmt->bindValue(1, $stock, PDO::PARAM_STR);
                           $stmt->bindValue(2, $product_id, PDO::PARAM_STR);
                          //実行
                           $stmt->execute();
                           
                           
                           //var_dump($sql);
                           
                    }catch (PDOException $e) {
                        $err_msg[] = $e->getMessage();
                         $dbh->rollback();
                          throw $e;
                      $err_msg[] = $value['product_name'].'の購入に失敗しました。右上からカート画面にお戻りください1';
                    }
                    }else{
                        $err_msg[] = $value['product_name'].'は販売可能な商品ではありません。右上からカート画面にお戻りください';
                    }
                    }else{
                       $err_msg[] = $value['product_name'].'の選択した数量が在庫を上回ってます。右上からカート画面にお戻りください';
                       }
                 
                 }
                  if(count($err_msg) ===0){
                      
                 try{
                    $sql = "DELETE
                            FROM cart
                            WHERE user_id = ?";
                
                  // SQL文を実行する準備
                  $stmt = $dbh->prepare($sql);
                  // SQL文のプレースホルダに値をバインド
                  $stmt->bindValue(1, $user_id, PDO::PARAM_STR);
                  // SQLを実行
                  $stmt->execute();
                  
                  // コミット処理
                  $dbh->commit();
                  
                 
              // 接続失敗した場合
              }catch (PDOException $e) {
                 $err_msg[] = $e->getMessage();
               }
               
               
                  }else{//96行
                   }
              }//58行
}//18行

}//15行
?>

<!DOCTYPE html>
<html lang = "ja">
<head>
 <meta charset="utf-8">
 <title>購入結果画面</title>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/10up-sanitize.css/4.1.0/sanitize.min.css">
 <style>
     body{
      width: 1000px;
      margin-left: 25px;
     
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
    .nocart{
        text-align:center;
      margin-top:200px;
    }
    .tag{
   display: flex;
   justify-content: flex-end;
   margin: auto;
 }
 .rr{
     text-align:center;
     font-size :20px;
 }
 .logout{
     text-align:center;
     margin-top:30px;
     font-size:18px;
 }
 </style>
 </head>
 <body>
     <header>
      <h1><a href="main.php">pa de chat</a></h1>
    </header>
  <div class="tag">
    <div class="log in"style="width:100px; text-align:center;">ID:<?php print$_SESSION['id']; ?></div>
      <div>|</div>
      <div class="log out"style="width:150px; text-align:center;"><a href="logout.php">LOG OUT</a></div>
      <div>|</div>
      <div class="cart"style="width:150px; text-align:center;"><a href="cart.php">カートへ</a></div>
      <div>|</div>
      <div class='home' style="width:150px;text-align:center;"><a href="main.php">HOME</a></div>
   </div>
    <?php if(count(err_msg) >=1){ ?>
 <?php foreach ($err_msg as $value) {?>
    <div class="nocart"><?php print $value; ?></div>
    <?php } ?>
    <?php } ?>
    
   <?php if(count($err_msg) ===0){ ?>
    <?php foreach($data as $value){ ?>
     <div class='product'>
      <div class="photo">
        <img src="<?php print $img_dir . $value['img']; ?>"width="130" height="170">
      </div>
      <div class='name'>
        <div><?php print $value['product_name']; ?></div>
        <div><?php print number_format($value['price']);?>円(税抜)</div>
        <div>数量：<?php print $value['num']; ?></div>
      </div>
    </div>
    <?php $total=0; ?>
    <?php $total = $total+$value['price']*$value['num']; ?>
   <?php } ?>
 
   <div class="rr">上記合計¥<?php print number_format($_POST['total']*1.08+100); ?>(tax、送料込)の購入が完了しました</div>
   <div class="logout"><a href="logout.php">ログアウトするにはこちらを押してください</a></div>
   <?php } ?>
</body>
</html>

<?php //var_dump($user_id); 
//var_dump($rows);
//var_dump($stock);
//var_dump($err_msg);
?>