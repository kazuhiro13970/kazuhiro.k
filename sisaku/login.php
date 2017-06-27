<?php
	$db = mysqli_connect('localhost', 'kazuhiro13970', '', 'c9') or die(mysqli_connect_error());
	mysqli_set_charset($db, 'utf8');
	
session_start();

if(!empty($_POST)){
  //ログインの処理
  if($_POST['mail'] != '' && $_POST['pass'] != ''){
    $sql = sprintf('SELECT * FROM users WHERE mail="%s" AND pass="%s"',
      mysqli_real_escape_string($db, $_POST['mail'])
      );
    $record = mysqli_query($db, $sql) or die(mysqli_error($db));
    if($table = mysqli_fetch_assoc($record)){
      //ログイン成功
      header('Location: index1.php');
      exit();
    }else{
      $error['login'] = 'failed';
    }
  }else{
    $error['login'] = 'blank';
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>ログイン</title>
  </head>
  <body>
  	<p>メールアドレスとパスワードを記入してログインしてください。</p>
  	<p>入会手続きがまだの方はこちらからどうぞ。</p>
  	<p>&raquo;<a href="join/">入会手続きをする</a></p>
  	<form action="" method="post">
  		<<dl>
	<dt>メールアドレス</dt>
	<dd>
		<input type="text" name="mail" size="35" maxlength="255"
				value="<?php echo htmlspecialchars($_POST['mail']); ?>">
		<?php if(!empty($error['login']) && $error['login'] == 'blank'): ?>
			<p><font color="red">* メールアドレスとパスワードをご記入ください</font></p>
		<?php endif; ?>
		<?php if(!empty($error['login']) && $error['login'] == 'failed'): ?>
			<p><font color="red">* ログインに失敗しました。正しくご記入ください。</font></p>
		<?php endif; ?>
	</dd>
	<dt>パスワード</dt>
	<dd>
		<input type="password" name="pass" size="35" maxlength="255"
				value="<?php echo htmlspecialchars($_POST['pass']); ?>">
	</dd>
</dl>
  		<input type="submit" value="ログインする">
  	</form>
  </body>
</html>