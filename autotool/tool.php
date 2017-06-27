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


    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

//商品名のエラーメッセージ設定
 if($name ===''){
   $err_msg[] = '商品名を入力してください';
 }else if(mb_strlen($name) > 30){
   $err_msg[] = '商品名は30文字以内で入力してください';
 }

 //価格のエラーメッセージ設定
 if($price ===''){
   $err_msg[] = '価格を入力してください';
 }else if((preg_match('/^([0-9]{1,5})$/',$price)) !== 1){
   $err_msg[] = '価格を整数5桁まででで入力してください';
 }

 //個数のエラーメッセージ設定
 if($stock === ''){
   $err_msg[] = '個数を入力してください';
 }else if((preg_match('/^([0-9]{1,5})$/',$price)) !== 1){
   $err_msg[] = '個数は整数5桁までで入力してください';
 }
 }

 //ドリンクIDを＄drink_idに詰め替え
 if(isset($_POST['drink_id']) === TRUE){
   $drink_id = $_POST['drink_id'];
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
    $status = $_POST['status'];

      // SQL文を作成
      $sql = 'INSERT INTO drink_master(drink_name,price,img,status,create_datetime) VALUES(?,?,?,?,?)';
      // SQL文を実行する準備
      $stmt = $dbh->prepare($sql);
      // SQL文のプレースホルダに値をバインド
      $stmt->bindValue(1, $name, PDO::PARAM_STR);
      $stmt->bindValue(2, $price, PDO::PARAM_INT);
      $stmt->bindValue(3, $new_img_filename, PDO::PARAM_STR);
      $stmt->bindValue(4, $status, PDO::PARAM_STR);
      $stmt->bindValue(5, $create_datetime, PDO::PARAM_STR);
       // SQLを実行
      $stmt->execute();

      $drink_id = $dbh->lastInsertId('drink_id');
      $stock = $_POST['stock'];

      //sql文作成ストックの方にインセート
      $sql = 'INSERT INTO drink_stock(drink_id,stock, create_datetime) VALUES(?,?,?)';
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
      $result_msg = '商品を追加しました';
    }
    }catch (PDOException $e) {
      $dbh->rollback();
    throw $e;
   }


//update時の変数作り
 }else if($sql_kind === 'update'){
$update_stock = $_POST['update_stock'];

if($update_stock === ''){
  $err_msg[] = '個数を入力してください';
}else if((preg_match('/^([0-9]{1,5})$/',$update_stock)) !== 1){
  $err_msg[] = '個数は整数5桁までで入力してください';
}
//アップデート処理開始
try{
if(count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
   //在庫数を$update_stockに詰め替え
   if(isset($_POST['update_stock']) === TRUE){
     $update_datetime=date('Y-m-d H:i:s');
   }


    //在庫のアップデート
     $sql = 'UPDATE drink_stock SET stock = ?,update_datetime = ? WHERE drink_id = ?';
     $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $update_stock, PDO::PARAM_INT);
        $stmt->bindValue(2, $update_datetime, PDO::PARAM_STR);
        $stmt->bindValue(3, $drink_id, PDO::PARAM_INT);
        $stmt->execute();
        $result_msg = '在庫を更新しました';
      }
      } catch (PDOException $e) {
          $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
      }



  //ステータスの変更
    }else if($sql_kind === 'update_status'){
      $status = $_POST['status'];
      if($status === '1'){
        $update_status = '0';
      }else if($status === '0'){
        $update_status ='1';
      }
      //ステータスのアップデート
      try{
        $sql = 'UPDATE drink_master SET status = ? WHERE drink_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$update_status,PDO::PARAM_INT);
        $stmt->bindValue(2,$drink_id,PDO::PARAM_INT);
        $stmt->execute();
        $result_msg = 'ステータスを変更しました。';
      }catch (PDOException $e){
        $err_msg['db_connect'] = 'DBエラー:'.$e->getMessage();
      }
    }


  }



 //SELECT文を表示するdrink
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/10up-sanitize.css/4.1.0/sanitize.min.css">

  <style>

    body {
      margin-left: 10px;
    }
    table {
      width: 800px;
      border-collapse: collapse;
    }
    table, tr, th, td {
      border: solid 1px;
      padding: 10px;
      text-align: center
    }

    .input_menu_width{
      width: 200px;
      border: solid 1px;
    }
    .input_text_width{
      width: 50px;
      border: solid 1px;
      text-align: right;
    }
    .submit_color{
      background-color: lightcyan;
      cursor: pointer;
      cursor: hand;
    }
    .select_color{
      background-color: lightgray;
    }
    .s1{
      width:300px;
    }
    .s2{
      width:200px;
    }
    .s3{
      width:100px;
    }
    .s4.{
      width:100px;
    }
    .s5{
      width: 100px;
    }


    .name1{
      width: px;
    }
    .name2{
      width: px;
    }
    .name3{
      width: px;
    }
    .name4{
      width: px;
    }
    .name5{
      width: px
    }


  </style>
</head>
<body>
  <?php foreach ($err_msg as $value) {?>
  <p><?php print $value;?></p><?php } ?>

  <?php if ( !empty($result_msg) ) { ?>
    <p><?php print $result_msg; ?></p>
  <?php } ?>


<header>
  <h1>自動販売機管理ツール</h1>
</header>
<div class="menu">
<p>新規商品追加</p>

<form method = "POST" enctype="multipart/form-data">
 <label>商品名</label>
 　　<input type='text' name='name' class="input_menu_width"><br>
 <label>値段</label>
 　　<input type='text' name='price' class="input_menu_width"><br><!-- type='numberにしてインボックに半角数字でと喜寿するるのもあり-->
 <label>個数</label>
 　　<input type='text' name='stock' class="input_menu_width"><br>
 <input type="file" name="new_img"><br>
 　<select name= "status" class="select_color">
  　 <option value="0">非公開</option>
  　 <option value="1">公開</option>
  </select><br>
  <input type="submit" value="追加する" class="submit_color">
  <input type="hidden" name="sql_kind" value="insert">
</form>

</div>

<div class="all_product">
<h2>商品情報変更</h2>
<p>商品一覧</p>
<table border="1">
<tr>
  <th class="s1">商品画像</th>
  <th class="s2">商品名</th>
  <th class="s3">価格</th>
  <th class="s4">個数</th>
  <th class="s5">ステータス</th>
</tr>


  <tr class="all">

    <?php foreach ($data as $value)  { ?>
    <td class="name1"><img src="<?php print $img_dir . $value['img']; ?>"width="150" height="150"></td>

    <td class="name2"><?php print $value['drink_name'];?></td>

    <td class="name3"><?php print $value['price'];?></td>

    <td class="name4">
      <form method="post">
      <input type="text"  class="input_text_width" name='update_stock' value= <?php print $value['stock'];?>>個
      <input type="submit" value="変更" class="submit_color">
      <input type="hidden" name="sql_kind" value="update">
      <input type="hidden" name="drink_id" value ="<?php print $value['drink_id'];?>">
    </form>
    </td>

    <td class="name5">
    <form method="POST" >
      <input type="submit" name ="" class="submit_color"
      value=<?php if($value['status'] === 0):?>
                非公開から公開にする
          <?php elseif($value['status'] === 1):?>
                公開から非公開にする
          <?php endif;?>/>
      <input type="hidden" name="sql_kind" value="update_status">
      <input type="hidden" name"update_status">
      <input type="hidden" name="drink_id" value="<?php print $value['drink_id'];?>">
      <input type="hidden" name="status" value="<?php print $value['status'];?>">
    </form>
  </td>

</tr>
  <?php } ?>
  </table>
</div>

</body>
</html>