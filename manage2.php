<?php

$host     = 'localhost';
$username = 'kazuhiro13970';   // MySQLのユーザ名
$password = '';   // MySQLのパスワード
$dbname   = 'camp';   // MySQLのDB名
$charset  = 'utf8';   // データベースの文字コード

// MySQL用のDSN文字列
$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

$img_dir    = './img/';   // アップロードした画像ファイルの保存ディレクトリ
$data       = [];
$err_msg    = [];         // エラーメッセージ
$new_img_filename = '';   // アップロードした新しい画像ファイル名
//間違い $_FILES   ='';

// アップロード画像ファイルの保存
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //POSTのvalueがinsertの時
  if($_POST['sql_kind'] === 'insert'){
      // HTTP POST でファイルがアップロードされたかどうかチェック
		if (is_uploaded_file($_FILES['new_img']['tmp_name']) === TRUE) {
			// 画像の拡張子を取得
			$extension = pathinfo($_FILES['new_img']['name'], PATHINFO_EXTENSION);
	        	// 指定の拡張子であるかどうかチェック
				if ($extension === 'jpg' || $extension === 'jpeg') {
					// 保存する新しいファイル名の生成（ユニークな値を設定する）
					$new_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;
			           // 同名ファイルが存在するかどうかチェック
						if (is_file($img_dir . $new_img_filename) !== TRUE) {
							// アップロードされたファイルを指定ディレクトリに移動して保存
							if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $new_img_filename) !== TRUE) {
							$err_msg[] = 'ファイルアップロードに失敗しました';
							}
						} else {
						$err_msg[] = 'ファイルアップロードに失敗しました。再度お試しください。';}
				} else {
				$err_msg[] = 'ファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';}
		} else {
		$err_msg[] = 'ファイルを選択してください';
    }
//商品名のエラーメッセージ設定
 if((isset($_POST['name'])) === TRUE){
   $name = $_POST['name'];
 }
 if($name ===''){
   $err_msg[] = '商品名を入力してください';
 }else if(mb_strlen($name) > 30){
   $err_msg[] = '商品名は30文字以内で入力してください';
 }

 //価格のエラーメッセージ設定
 if(isset($_POST['price']) === TRUE){
   $price = $_POST['price'];
 }
 if($price ===''){
   $err_msg[] = '価格を入力してください';
 }else if((preg_match('/[0-9]/',$price)) === FALSE){
   $err_msg[] = '価格は半角数字で入力してください';
 }

 //個数のエラーメッセージ設定
 if(isset($_POST['stock']) === TRUE){
   $stock = $_POST['stock'];
 }
 if($stock === ''){
   $err_msg[] = '個数を入力してください';
 }else if((preg_match('/[0-9]/',$stock)) === FALSE){
   $err_msg[] = '個数は半角数字で入力してください';
 }
 }
}

// アップロードした画像、名前、価格を登録
try {
  // データベースに接続
  $dbh = new PDO($dsn, $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  //POSTした時にsql_kindにhiddenのvalueを代入
  if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['sql_kind'])){
    $sql_kind = $_POST['sql_kind'];
  }


  // 送られてきた非表示データに応じて処理を振り分けます。
  if ($sql_kind === 'insert') {

    // トランザクション開始
    $dbh->beginTransaction();
    try{

  // エラーがなければ、アップロードしたデータを保存
  if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $create_datetime = date('Y-m-d H:i:s');

      // SQL文を作成
      $sql = 'INSERT INTO test_drink_master(drink_name,price,img,create_datetime) VALUES(?,?,?,?)';
      // SQL文を実行する準備
      $stmt = $dbh->prepare($sql);
      // SQL文のプレースホルダに値をバインド
      $stmt->bindValue(1, $name, PDO::PARAM_STR);
      $stmt->bindValue(2, $price, PDO::PARAM_INT);
      $stmt->bindValue(3, $new_img_filename, PDO::PARAM_STR);
      $stmt->bindValue(4, $create_datetime, PDO::PARAM_STR);
       // SQLを実行
      $stmt->execute();

      $drink_id = $dbh->lastInsertId('drink_id');
      $stock = $_POST['stock'];
      //sql文作成ストックの方にインセート
      $sql = 'INSERT INTO test_drink_stock(drink_id,stock, create_datetime) VALUES(?,?,?)';
      //sql文を実行する準備
      $stmt = $dbh->prepare($sql);
      //sql文のプレースホルダに値を入れる
      $stmt->bindValue(1,$drink_id, PDO::PARAM_INT);
      $stmt->bindValue(2,$stock,PDO::PARAM_STR);
      $stmt->bindValue(3,$create_datetime,PDO::PARAM_STR);
      //sql文を実行
      $stmt->execute();
      // コミット処理
      $dbh->commit();
      echo 'データ登録ができました';//作成途中であえてechoをつけてます
    }
    }catch (PDOException $e) {
      $dbh->rollback();
    throw $e;
   }



 }else if($sql_kind === 'update'){
$update_stock = $_POST['update_stock'];
if($_SERVER['REQUEST_METHOD'] === 'POST') {
   if($_POST['sql_kind'] === 'update'){
   //在庫数を$update_stockに詰め替え
   if(isset($_POST['update_stock']) === TRUE){
     $update_datetime=date('Y-m-d H:i:s');
   }
   //ドリンクIDを＄drink_idに詰め替え
   if(isset($_POST['drink_id']) === TRUE){
     $drink_id = $_POST['drink_id'];
   }
}
}
    //在庫のアップデート
      try{
     $sql = 'UPDATE test_drink_stock SET stock = ?,update_datetime = ? WHERE drink_id = ?';
     $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $update_stock, PDO::PARAM_INT);
        $stmt->bindValue(2, $update_datetime, PDO::PARAM_STR);
        $stmt->bindValue(3, $drink_id, PDO::PARAM_INT);
        $stmt->execute();
      } catch (PDOException $e) {
          $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
      }
    }
  }


 //SELECT文を表示するdrink
try{
    // SQL文を作成　参照する
    $sql = 'SELECT test_drink_master.drink_id,
                   test_drink_master.img,
                   test_drink_master.drink_name,
                   test_drink_master.price,
                   test_drink_stock.stock
            FROM test_drink_master INNER JOIN test_drink_stock
           ON test_drink_master.drink_id = test_drink_stock.drink_id
           ORDER BY test_drink_master.drink_id desc';

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


  }catch (PDOException $e) {
  // 接続失敗した場合
    $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
   }
?>

<!DOCTYPE html>
<html lang='ja'>
<head>
  <meta charset= 'UTF-8'>
  <title>自動販売機</title>
</head>
<body>

<header>
  <h1>自動販売機管理ツール</h1>
</header>
<div class="menu">
<p>新規商品追加</p>

<form method = "POST" enctype="multipart/form-data">
 <label>商品名</label>
 <input type='text' name='name'><br>
 <label>値段</label>
 <input type='text' name='price'><br><!-- type='numberにしてインボックに半角数字でと喜寿するるのもあり-->
 <label>個数</label>
 <input type='text' name='stock'><br>
 <input type="file" name="new_img"><br>
 <input type="submit" value="追加する">
  <input type="hidden" name="sql_kind" value="insert">
</form>
<?php foreach ($err_msg as $value) {?>
  <p><?php print $value; ?></p>
<?php } ?>
</div>

<div class="all_product">
<h2>商品情報変更</h2>
<p>商品一覧</p>
<table border="1">
<tr>
  <th>商品画像</th>
  <th>商品名</th>
  <th>価格</th>
  <th>個数</th>
</tr>
<tr>
    <?php foreach ($data as $value)  { ?>
    <td><img src="<?php print $img_dir . $value['img']; ?>"></td>
    <td><?php print $value['drink_name'];?></td>
    <td><?php print $value['price'];?></td>
    <td>
      <form method="post">
      <input type="text" name='update_stock' value= <?php print $value['stock'];?>個
      <input type="submit" value="変更">
      <input type="hidden" name="sql_kind" value="update">
      <input type="hidden" name="drink_id" value ="<?php print $value['drink_id'];?>">
    </form>

    </td>
<tr>
  <?php } ?>
  </table>


</body>
</html>
