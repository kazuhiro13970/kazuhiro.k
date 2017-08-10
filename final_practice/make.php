<?php
// DBとの接続
include_once 'dbconnect.php';

$img_dir    = './img1/';   // アップロードした画像ファイルの保存ディレクトリ
$data       = [];
$err_msg    = [];         // エラーメッセージ
$result_msg = [];
$new_img_filename = '';   // アップロードした新しい画像ファイル名

// アップロード画像ファイルの保存
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //POSTのvalueがinsertの時
  if($_POST['sql_kind'] === 'insert'){
      // HTTP POST でファイルがアップロードされたかどうかチェック
		if (is_uploaded_file($_FILES['new_img1']['tmp_name']) === TRUE) {
			// 画像の拡張子を取得
			$extension = pathinfo($_FILES['new_img1']['name'], PATHINFO_EXTENSION);
	        	// 指定の拡張子であるかどうかチェック
				if ($extension === 'jpg' || $extension === 'jpeg' ||$extension === 'png' ||$extension === 'PNG' ||$extension === 'JPG' ||$extension === 'JPEG') {
					  // 保存する新しいファイル名の生成（ユニークな値を設定する）
					  $new1_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;
			           // 同名ファイルが存在するかどうかチェック
						if (is_file($img_dir . $new1_img_filename) !== TRUE) {
							// アップロードされたファイルを指定ディレクトリに移動して保存
							if (move_uploaded_file($_FILES['new_img1']['tmp_name'], $img_dir . $new1_img_filename) !== TRUE) {
							$err_msg[] = '画像1のファイルアップロードに失敗しました';
							}
						} else {
						$err_msg[] = '画像1のファイルアップロードに失敗しました。再度お試しください。';}
				    
			  	} else {
				$err_msg[] = '画像1のファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';}
		} else {
		$err_msg[] = '画像1のファイルを選択してください';
    }
    //画像2のファイル保存
    if (is_uploaded_file($_FILES['new_img2']['tmp_name']) === TRUE) {
			// 画像の拡張子を取得
			$extension = pathinfo($_FILES['new_img2']['name'], PATHINFO_EXTENSION);
	        	// 指定の拡張子であるかどうかチェック
				if ($extension === 'jpg' || $extension === 'jpeg' ||$extension === 'png' ||$extension === 'PNG' ||$extension === 'JPG' ||$extension === 'JPEG') {
					  // 保存する新しいファイル名の生成（ユニークな値を設定する）
					  $new2_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;
			           // 同名ファイルが存在するかどうかチェック
						if (is_file($img_dir . $new2_img_filename) !== TRUE) {
							// アップロードされたファイルを指定ディレクトリに移動して保存
							if (move_uploaded_file($_FILES['new_img2']['tmp_name'], $img_dir . $new2_img_filename) !== TRUE) {
							$err_msg[] = '画像2のファイルアップロードに失敗しました';
							}
						} else {
						$err_msg[] = '画像2のファイルアップロードに失敗しました。再度お試しください。';}
				    
			  	} else {
				$err_msg[] = '画像2のファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';}
		} else {
		$err_msg[] = '画像2のファイルを選択してください';
    }
    
    //変数作り
    $product_name = $_POST['product_name'];
    $brand_id = $_POST['brand_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $comment=$_POST['comment'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $genre = $_POST['genre'];
    $status = $_POST['status'];
    
  //商品名のエラーメッセージ設定
 if($name ===''){
   $err_msg[] = '商品名を入力してください';
 }else if(mb_strlen($product_name) > 100){
   $err_msg[] = '商品名は半角100文字、全角50文字以内で入力してください';
 }
 //ブランド名前のチェック
 if($brand_id === ''){
     $err_msg[] = 'ブランドを選択してください';
 }
 //価格のエラーメッセージ設定
 if($price ===''){
   $err_msg[] = '価格を入力してください';
 }else if((preg_match('/^([0-9]{1,7})$/',$price)) !== 1){
   $err_msg[] = '価格を整数7桁まででで入力してください';
 }
 //個数のエラーメッセージ設定
 if($stock === ''||$stock ==='0'){
   $err_msg[] = '個数を入力してください';
 }else if((preg_match('/^([0-9]{0,5})$/',$price)) !== 1){
   $err_msg[] = '個数は整数5桁までで入力してください';
 }
 //コメントのエラーメッセージ設定
 if($comment ===''){
   $err_msg[] = 'コメントを入力してください';
 }else if(mb_strlen($name) > 300){
   $err_msg[] = 'コメントは300文字以内で入力してください';
 }
 //サイズのエラーメッセージ設定
 if($size ===''){
     $err_msg[] = 'サイズ(寸法など)を入力してください';
 }else if(mb_strlen($size) >200){
     $err_msg[] = 'サイズは200文字以内で入力してください';
 }
 //カラーのエラーメッセージ設定
 if($color === ''){
     $err_msg[] = 'カラーを入力してください';
 }else if(mb_strlen($color) > 100){
     $err_msg[] = 'カラーは100文字以内で入力してください';
 }
 //ジャンルのエラーメッセージ設定
 if($genre === ''){
     $err_msg[] = 'ジャンルを選択してください';
 }
 
  }
  
  //codeを＄product_idに詰め替え
 if(isset($_POST['code']) === TRUE){
   $product_id = $_POST['code'];
 }
 //商品名の変数作り
 if(isset($_POST['product']) === TRUE){
     $product_name = $_POST['product'];
 }
}
 
     //insert時の条件
    if (count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['sql_kind'])){
      $sql_kind = $_POST['sql_kind'];
      }

      // 送られてきた非表示データに応じて処理を振り分け
      if ($sql_kind === 'insert') {

     try{
        // SQL文を作成
        $sql = 'INSERT INTO product(product_name,brand_id,price,img,img2,
                                    product_comment,size,color,genre,
                                    stock,status) 
                                    VALUES(?,?,?,?,?,?,?,?,?,?,?)';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $product_name, PDO::PARAM_STR);
        $stmt->bindValue(2, $brand_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $price, PDO::PARAM_STR);
        $stmt->bindValue(4, $new1_img_filename, PDO::PARAM_STR);
        $stmt->bindValue(5, $new2_img_filename, PDO::PARAM_STR);
        $stmt->bindValue(6, $comment, PDO::PARAM_STR);
        $stmt->bindValue(7, $size, PDO::PARAM_STR);
        $stmt->bindValue(8, $color, PDO::PARAM_STR);
        $stmt->bindValue(9, $genre, PDO::PARAM_STR);
        $stmt->bindValue(10, $stock, PDO::PARAM_STR);
        $stmt->bindValue(11, $status, PDO::PARAM_STR);
         // SQLを実行
        $stmt->execute();
        
        $result_msg[] = $product_name.'の追加に成功しました';
        
      }catch (PDOException $e) {
      $err_msg[] = $e->getMessage();
   }
   
}else if($sql_kind === 'update'){
    $update_stock = $_POST['update_stock'];
    $product_name = $_POST['product'];

if($update_stock === ''){
  $err_msg[] = '個数を入力してください';
}else if((preg_match('/^([0-9]{1,5})+$/',$update_stock)) !== 1){
  $err_msg[] = '個数は整数5桁までで入力してください';
}
//アップデート処理開始
if(count($err_msg) === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {

try{


    //在庫のアップデート
     $sql = 'UPDATE product SET stock = ? WHERE product_id = ?';
     $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $update_stock, PDO::PARAM_INT);
        $stmt->bindValue(2, $product_id, PDO::PARAM_STR);
        $stmt->execute();
        
        
        $result_msg[] = $product_name.'の在庫を更新しました';
      
      } catch (PDOException $e) {
          $err_msg['db_connect'] = 'DBエラー：'.$e->getMessage();
      }

}//ステータスの変更
    }else if($sql_kind === 'update_status'){
      $status = $_POST['status'];
      if($status === '1'){
        $update_status = '0';
      }else if($status === '0'){
        $update_status ='1';
      }
      
      //ステータスのアップデート
      try{
        $sql = 'UPDATE product SET status = ? WHERE product_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$update_status,PDO::PARAM_INT);
        $stmt->bindValue(2,$product_id,PDO::PARAM_INT);
        $stmt->execute();
        
        $result_msg[] = $product_name.'のステータスを変更しました。';
      }catch (PDOException $e){
        $err_msg['db_connect'] = 'DBエラー:'.$e->getMessage();
      }
      
      //データ消去
    }else if($sql_kind === 'delete'){
        
        
        try{
            $sql = " DELETE 
                     FROM  product
                     WHERE product_id = ?";
                     
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1,$product_id,PDO::PARAM_INT);
                $stmt->execute();
                
                $result_msg[] = $product_name.'を消去しました';
            
        }catch (PDOException $e){
        $err_msg['db_connect'] = 'DBエラー:'.$e->getMessage();
      }
    }
}
 //SELECT文を表示する商品一覧
try{
    // SQL文を作成　参照する
    $sql = 'SELECT product.product_id,
                   product.product_name,
                   product.brand_id,
                   product.price,
                   product.img,
                   product.img2,
                   product.product_comment,
                   product.size,
                   product.color,
                   product.genre,
                   product.stock,
                   product.status,
                   brand.brand_name
            FROM product  INNER JOIN brand
            ON product.brand_id = brand.brand_id
            ORDER BY brand_id';
            
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
<html lang='ja'>
<head>
  <meta charset= 'UTF-8'>
  <title>Pa De Chat商品管理ツール</title>
  <style>
  body{
      width:1000px;
      margin-bottom:40px;
  }
  
  .center{
      display:flex;
  }
  .left{
      margin-left:50px;
      margin-top:50px;
      margin-right:10px;
      font-size:20px;
      text-align:right;
  }
  .right{
      margin-top: 50px;
      font-size:30px;
  }
  input[type=text]{
      width:250px;
      height:30px;
      font-size:14px;
  }
  
  .list1{
      display:flex;
      font-size:14px;
  }
  .f1{
      flex-basis: 200px; flex-grow: 0;
  }
  .f2{
      flex-basis: 300px; flex-grow: 0;
  }
  .f3{
      flex-basis: 100px; flex-grow: 0;
  }
  .f4{
      flex-basis: 170px; flex-grow: 0;
  }
  .f5{
      flex-basis: 170px; flex-grow: 0;
  }
  .list2{
      display:flex;
      font-size:14px;
  }
  .flex1{
      flex-basis: 200px; flex-grow: 0;
  }
  .flex2{
      flex-basis: 200px; flex-grow: 0;
  }
  .flex3{
      flex-basis: 150px; flex-grow: 0;
  }
  .flex4{
      flex-basis: 250px; flex-grow: 0;
  }
  .flex5{
      flex-basis: 200px; flex-grow: 0;
  }
  .list3{
      display:flex;
      border-bottom:solid 1px;
      font-size:14px;
  }
  .delete{
      text-align:right;
      color:red;
  }
  
  </style>
</head>

<h1>商品管理ツール</h1>

<p>新規商品追加</p>

<?php foreach ($err_msg as $value) {?>
  <p><?php print $value;?></p><?php } ?>

  <?php foreach ($result_msg as $value) { ?>
    <p><?php print (htmlspecialchars($value)); ?></p>
  <?php } ?>

<form method = "POST" enctype="multipart/form-data">
<div class="center">
  <div class="left">
   <div style="margin-top:12px;">商品名</div>
   <div style="margin-top:18px;">ブランド名</div>
   <div style="margin-top:12px;">値段</div>
   <div style="margin-top:15px;">コメント</div>
   <div style="margin-top:17px;">サイズ</div>
   <div style="margin-top:16px;">カラー</div>
   <div style="margin-top:16px;">ジャンル</div>
   <div style="margin-top:15px;">在庫数</div>
   <div style="margin-top:17px">画像1</div>
   <div style="margin-top:17px">画像2</div>
   <div style="margin-top:16px;">ステータス</div>
  </div>
 
 <div class="right">
     <form method="POST">
    <div><input type='text' name='product_name'></div>
    <div><select name="brand_id">
           <option value="">選択してください</option>
           <option value="1")>ATTACHMENT</option>
           <option value="2">JAMES PERSE</option>
           <option value="3">côte&ciel</option></option>
           <option value="4">IWC</option>
           <option value="5">ITTI</option>
           <option value="6">その他</option>
         </select></div>
    <div><input type='text' name='price'></div>
    <div><input type='text' name='comment'></div>
    <div><input type='text' name='size'></div>
    <div><input type='text' name='color'></div>
    <div><select name="genre">
           <option value="">選択してください</option></option>
           <option value="1">OUTER</option></option>
           <option value="2">TOPS</option>
           <option value="3">BOTTOMS</option>
           <option value="4">SHOES</option></option>
           <option value="5">OTHER</option>
         </select></div>
    <div><input type='text' name='stock'></div>
    <div><input type="file" name="new_img1"></div>
    <div><input type="file" name="new_img2"></div>
    <div><select name= "status">
           <option value="1">公開</option>
           <option value="0">非公開</option>
         </select></div>
         
    <input type="hidden" name="sql_kind" value="insert">
      <p><input type="submit" value="追加する"></p>
    </form>
  </div>
 </div>
        
        
 <?php foreach ($data as $value)  { ?>
          
　<div class="list1">
　    <div class="f1">ブランド名：<?php print (htmlspecialchars($value['brand_name'])); ?></div>
　    <div class="f2">商品名：<?php print (htmlspecialchars($value['product_name'])); ?></div>
　    <div class="f3">ジャンル　<?php print $value['genre']; ?></div>
　    <div class="f4">価格：¥<?php print number_format($value['price']); ?></div>
　    <form method="post">
　    <div class="f5">在庫：<input type="text"  class="input_text_width" name='update_stock' value= <?php print $value['stock'];?>>個
                 <input type="submit" value="変更" class="submit_color">
                 <input type="hidden" name="sql_kind" value="update">
                 <input type="hidden" name="code" value ="<?php print $value['product_id'];?>">
                 <input type="hidden" name="product" value ="<?php print $value['product_name'];?>">
           </div></form>
　   </div>
　<div class="list2">
　    <div class="flex1"><img src="<?php print $img_dir . $value['img']; ?>"width="150" height="150"></div>
　    <div class="flex2"><img src="<?php print $img_dir . $value['img2']; ?>"width="150" height="150"></div>
　    <div class="flex3">カラー：<?php print (htmlspecialchars($value['color'])); ?></div>
　    <div class="flex4">サイズ：<?php print (htmlspecialchars($value['size'])); ?></div>
　    <form method="POST" >
　    <div class="flex5">ステータス：
      <input type="submit" name ="" class="submit_color" value=<?php if($value['status'] === 0):?>
                                                          非公開から公開にする
                                                        <?php elseif($value['status'] === 1):?>
                                                          公開から非公開にする
                                                         <?php endif;?>/>
      <input type="hidden" name="sql_kind" value="update_status">
      <input type="hidden" name"update_status">
      <input type="hidden" name="code" value="<?php print $value['product_id'];?>">
      <input type="hidden" name="status" value="<?php print $value['status'];?>"></div>
      <input type="hidden" name="product" value="<?php print $value['product_name'];?>">
　   </div></form>
　<div class="list3">
　    <div>コメント：<?php print $value['product_comment']; ?></div>
    </div>
    <form method="POST">
    <div class="delete">↑<input type="submit" name="" value="このデータを削除する"></div>
      <input type="hidden" name="sql_kind" value="delete">
      <input type="hidden" name="code" value="<?php print $value['product_id'];?>">
      <input type="hidden" name="product" value="<?php print $value['product_name'];?>">
      </form>
    <?php } ?>  
</dody>
</html>
