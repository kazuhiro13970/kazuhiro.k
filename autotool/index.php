<?php

$host     = 'localhost';
$username = 'kazuhiro13970';   // MySQLのユーザ名
$password = '';   // MySQLのパスワード
$dbname   = 'camp';   // MySQLのDB名
$charset  = 'utf8';   // データベースの文字コード
$img_dir    = './img/';

// MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
// データベースに接続
$dbh = new PDO($dsn, $username, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

try{
    // SQL文を作成　参照する
    $sql = 'SELECT drink_master.drink_id,
                   drink_master.img,
                   drink_master.drink_name,
                   drink_master.price,
                   drink_stock.stock,
                   drink_master.status
            FROM drink_master INNER JOIN drink_stock
           ON drink_master.drink_id = drink_stock.drink_id
           ORDER BY drink_master.drink_id desc';

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
  }catch (PDOException $e) {
    // 接続失敗した場合
    $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
    }//セレクト文のみで完結
?>


<!DOCTYPE html>
<html lang = "ja">
<head>
 <meta charset="utf-8">
 <title>自動販売機</title>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/10up-sanitize.css/4.1.0/sanitize.min.css">
<style>
   body{
     width: 700px;
   }

   .product{
     margin: 5px;
     width: 150px;
     height:200px;
     float: left;
    text-align: center;
   }

   span{
     display: block;
   }

   .submit{
     clear:both;
   }
   .out{
     color: red;
   }
   
   .submit_color{
      background-color: lightcyan;
      cursor: pointer;
      cursor: hand;
   }
   
   .input_text_width{
       width: 200px;
      border: solid 1px;
   }
    


</style>

 </head>

<body>

  <h1>自動販売機</h1>
<form action ="result.php" method="POST">
 <div>金額
   <input type="text" class="input_text_width" name="money"value>
 </div>

<div class="id">

<?php foreach ($data as $value)  { ?>
  <?php if($value['status'] === 1):?>
  <div class="product">

     <span class=img_size>
       <img src="<?php print $img_dir . $value['img']; ?>"  width="130" height="120">
     </span>

     <span><?php print $value['drink_name'];?></span>

     <span><?php print $value['price'];?></span>

     <span>
       <?php if($value['stock'] === 0):?>
                 <span class="out">売り切れ</span>
           <?php elseif($value['stock'] >= 1):?>
             <span><input type="radio" name="drink_id" value="<?php print $value["drink_id"]?>"></span>

           <?php endif;?>


      </span>
    <?php endif;?>
   </div>
   <?php } ?>

</div>
<div class="submit" style=margin-left:50px;>
  <input type=submit value="購入する"  class="submit_color">
</div>
</form>

</body>
</html>