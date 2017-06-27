<?php
session_start();
	$db = mysqli_connect('localhost', 'kazuhiro13970', '', 'c9') or die(mysqli_connect_error());
	mysqli_set_charset($db, 'utf8');

if(!isset($_SESSION['join'])){
	header('Location: index1.php');
	exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	//登録処理をする
	$sql = sprintf('INSERT INTO loginpage SET name="%s", mail="%s", pass="%s"',
		mysqli_real_escape_string($db, $_SESSION['join']['name']),
		mysqli_real_escape_string($db, $_SESSION['join']['mail']),
		mysqli_real_escape_string($db, sha1($_SESSION['join']['pass']))
	);
	mysqli_query($db, $sql) or die(mysqli_error($db));
	unset($_SESSION['join']);

	header('Location: write.php');
	exit();
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>新規会員登録</title>
</head>
<body>
  <form action="" method="POST">
<dl>
  <dt>ユーザー名</dt>
  <dd>
    <?php echo htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES, 'UTF-8'); ?>
  </dd>
  <dt>メールアドレス</dt>
  <dd>
    <?php echo htmlspecialchars($_SESSION['join']['mail'], ENT_QUOTES, 'UTF-8'); ?>
  </dd>
  <dt>パスワード</dt>
  <dd>
    【表示されません】
  </dd>
</dl>
<div><a href="index1.php?action=rewrite">&laquo;&nbsp;書き直す</a>
<input type="submit" value="登録する"></div>
</form>
</body>
</html>