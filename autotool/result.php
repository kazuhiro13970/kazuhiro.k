<?php
$result_msg = []; //結果メッセージ
$err_msg = [];
$img_dir    = './img/';   // アップロードした画像ファイルの保存ディレクトリ
$drink_id = "";
$money = "";


 $host     = 'localhost';
 $username = 'kazuhiro13970';   // MySQLのユーザ名
 $password = '';   // MySQLのパスワード
 $dbname   = 'camp';   // MySQLのDB名
 $charset  = 'utf8';   // データベースの文字コード
 $img_dir    = './img/';
 // MySQL用のDSN文字列
 $dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;


 if($_SERVER['REQUEST_METHOD'] === 'POST'){
   $drink_id = $_POST['drink_id'];
   $money = $_POST['money'];

 }


   $dbh = new PDO($dsn, $username, $password);
   $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


     if((is_null($drink_id)) !==TRUE){

       try{
     //POSTされたドリンクIDカラムの情報読み出し
     $sql = "SELECT drink_master.drink_id,
                    drink_master.img,
                    drink_master.drink_name,
                    drink_master.price,
                    drink_stock.stock,
                    drink_master.status
             FROM drink_master INNER JOIN drink_stock
             ON drink_master.drink_id = drink_stock.drink_id
             WHERE drink_master.drink_id = $drink_id
             AND   drink_stock.drink_id = $drink_id";

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
      //ドリンクID固定時の変数作り
       foreach ($data as $value)  {
       $img = $value['img'];
       $drink_name = $value['drink_name'];
       $price = $value['price'];
       $stock = $value['stock'];
       $status = $value['status'];
      }

     }catch (PDOException $e) {
       // 接続失敗した場合
       $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
       }//セレクト文のみで完結
}

    if(is_null($drink_id) === TRUE){
         $err_msg[] = '商品を選択してください';
       }


         if($money === 0 || $money === ""){
              $err_msg[] = 'お金を投入してください';
           }else if($money >=10001){
              $err_msg[] = '10000円以下にしてください。';
            }else if((preg_match('/^([0-9]{1,5})$/',$price)) !== 1){
              $err_msg[] = '半角数字で入力してください。';
             }else if($money < $price){
              $err_msg[] = 'お金が足りません！';
             }else if($status === 0){
                $err_msg[] = '公開商品ではありません';
              }else if($stock === 0){
                $err_msg[] = '売り切れです';
              }else{
                $result_msg[] =  '<img src='.$img_dir . $img.'>';
                $result_msg[] = $drink_name.'が買えました。';
                $result_msg[] = 'お釣りは'.($money-$price).'円です';
              }


    if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST' ) {

      // トランザクション開始
      $dbh->beginTransaction();
      try{
        $create_datetime = date('Y-m-d H:i:s');
        $update_datetime = date('Y-m-d H:i:s');
        $stock = ($stock-1);

          // SQL文を作成
          $sql = 'INSERT INTO drink_history(drink_id,create_datetime) VALUES(?,?)';
          // SQL文を実行する準備
          $stmt = $dbh->prepare($sql);
          // SQL文のプレースホルダに値をバインド
          $stmt->bindValue(1, $drink_id, PDO::PARAM_STR);
          $stmt->bindValue(2, $create_datetime, PDO::PARAM_INT);
           // SQLを実行
          $stmt->execute();


          //sql文作成ストックの方にインセート
          $sql = 'UPDATE drink_stock SET stock = ?, update_datetime = ? WHERE drink_id = ?';
          //sql文を実行する準備
          $stmt = $dbh->prepare($sql);
          //sql文のプレースホルダに値を入れる
          $stmt->bindValue(1,$stock, PDO::PARAM_INT);
          $stmt->bindValue(2,$update_datetime,PDO::PARAM_STR);
          $stmt->bindValue(3, $drink_id, PDO::PARAM_INT);

          //sql文を実行
          $stmt->execute();
          // コミット処理
          $dbh->commit();

        }catch (PDOException $e) {
          $dbh->rollback();
        throw $e;
        $err_msg = '購入に失敗しました。';
       }
     }




 ?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>自動販売機結果</title>
</head>
<body>
  <h1>自動販売機結果</h1>

  <?php foreach ($err_msg as $value) {?>
    <p><?php print $value; ?></p>
  <?php } ?>
  <?php foreach ($result_msg as $value) {?>
    <p><?php print $value; ?></p>
  <?php } ?>


  <footer>
    <a href="index.php">戻る</a>
  </body>
</html>